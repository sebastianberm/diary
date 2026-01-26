<div wire:ignore x-data="richEditor({
        content: @entangle('content'),
        people: {{ json_encode($people) }},
        delay: {{ $scanDelay }}
     })" class="w-full">
    <!-- Editor Container -->
    <div x-ref="editor"
        class="prose dark:prose-invert max-w-none w-full min-h-[400px] border-none focus:ring-0 text-lg leading-relaxed text-gray-800 dark:text-gray-200 bg-transparent resize-none p-4 placeholder-gray-300 dark:placeholder-primary-700 font-serif">
    </div>

    <!-- Tiptap Logic via CDN -->
    <script type="module">
        import { Editor } from 'https://esm.sh/@tiptap/core';
        import StarterKit from 'https://esm.sh/@tiptap/starter-kit';
        import Placeholder from 'https://esm.sh/@tiptap/extension-placeholder';
        import Highlight from 'https://esm.sh/@tiptap/extension-highlight';

        document.addEventListener('alpine:init', () => {
            Alpine.data('richEditor', ({ content, people, delay }) => ({
                editor: null,
                content: content,
                timer: null,

                init() {
                    const _this = this;

                    this.editor = new Editor({
                        element: this.$refs.editor,
                        extensions: [
                            StarterKit,
                            Placeholder.configure({
                                placeholder: 'Dear Diary, today was...',
                            }),
                            Highlight.configure({ multipart: true }),
                        ],
                        content: this.content,
                        editorProps: {
                            attributes: {
                                class: 'outline-none h-full',
                            },
                        },
                        onUpdate({ editor }) {
                            _this.content = editor.getHTML();

                            // Debounced Auto-Scan
                            clearTimeout(_this.timer);
                            _this.timer = setTimeout(() => {
                                _this.$dispatch('request-scan');
                            }, delay);

                            // Real-time Highlighting (Local)
                            _this.highlightKnownPeople(editor);
                        },
                    });

                    // Initial highlight
                    this.highlightKnownPeople(this.editor);

                    // Sync backend content changes
                    this.$watch('content', (value) => {
                        if (!this.editor) return;

                        const currentHTML = this.editor.getHTML();
                        if (currentHTML === value) return;

                        // Only update if not focused to avoid cursor jumps/mismatches during typing
                        // Or if the change is significant (not just a minor HTML variation)
                        if (!this.editor.isFocused) {
                            this.editor.commands.setContent(value, false);
                        }
                    });
                },

                highlightKnownPeople(editor) {
                    if (!editor || !people || people.length === 0) return;

                    const allKeywords = people.flatMap(p => [p.name, ...(p.keywords || [])])
                        .filter(k => k && k.trim() !== '')
                        .map(k => k.trim());

                    if (allKeywords.length === 0) return;

                    // Regex for all keywords with word boundaries
                    const regex = new RegExp(`\\b(${allKeywords.map(k => k.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')).join('|')})\\b`, 'gi');

                    // We use the Highlight mark that's already configured
                    // First, clear existing highlights to avoid overlapping/stale ones
                    // Note: We do this only if not focused to avoid interrupting the user's cursor
                    // Wait, if it's debounced, it's fine.
                    
                    const { from, to } = editor.state.selection;
                    
                    // We'll apply the highlight mark to all found ranges
                    editor.chain().focus()
                        .unsetHighlight()
                        .command(({ tr, state }) => {
                            const { doc } = state;
                            doc.descendants((node, pos) => {
                                if (node.isText) {
                                    let match;
                                    while ((match = regex.exec(node.text)) !== null) {
                                        const start = pos + match.index;
                                        const end = start + match[0].length;
                                        tr.addMark(start, end, state.schema.marks.highlight.create());
                                    }
                                }
                            });
                            return true;
                        })
                        .setTextSelection({ from, to }) // Restore selection
                        .run();
                }
            }));
        });
    </script>

        .ProseMirror mark {
            background-color: #fef08a; /* gold-200 */
            color: inherit;
            border-radius: 0.25rem;
            padding: 0 0.125rem;
        }
        .dark .ProseMirror mark {
            background-color: #854d0e; /* gold-800 */
            color: #fef9c3; /* gold-100 */
        }
    </style>
</div>