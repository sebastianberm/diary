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
                        if (this.editor.getHTML() === value) return;
                        this.editor.commands.setContent(value, false);
                    });
                },

                highlightKnownPeople(editor) {
                    // Simple text search and highlight
                    // Note: Ideally this is a custom Tiptap extension, but for quick implementation:
                    // We can just scan text. Tiptap Highlight extension is manually applied to ranges.
                    // A proper implementation requires a decoraton or mark extension which matches regex.

                    // For now, let's rely on the user observing the 'Context Scan' button triggering or
                    // the detected list updating below.
                    // If we want IN-EDITOR highlighting, we need to iterate text nodes.
                }
            }));
        });
    </script>

    <style>
        .ProseMirror p.is-editor-empty:first-child::before {
            color: #adb5bd;
            content: attr(data-placeholder);
            float: left;
            height: 0;
            pointer-events: none;
        }
    </style>
</div>