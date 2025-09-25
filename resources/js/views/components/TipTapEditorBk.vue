<template>
    <div>
        <froala ref="froala" :tag="'textarea'" :config="FConfig" />
    </div>
</template>
<script>
export default {
    name: 'TipTapEditor',
    props: {
        modelValue: {
            type: String,
            default: '',
        },
        config: {
            type: Object,
            default: () => ({
                placeholderText: 'Write text here...',
                charCounterCount: true,
                toolbarButtons: [
                    'bold', 'italic', 'underline', 'strikeThrough',
                    'subscript', 'superscript',
                    'undo', 'redo',
                    'fontFamily', 'fontSize',
                    'color', 'backgroundColor',
                    'align', 'formatOL', 'formatUL', 'outdent', 'indent', 'quote',
                    'insertLink', 'insertTable',
                    'emoticons', 'specialCharacters', 'insertHR', 'selectAll', 'clearFormatting', 'html'
                ],
                heightMin: 300,
            }),
        },
    },

    computed: {
        FConfig() {
            return {
                ...this.config,
                events: {
                    initialized: () => {
                        this.setEditorContent(this.modelValue);
                    },
                    contentChanged: () => {
                        const editor = this.getEditorInstance();
                        if (editor) {
                            const html = editor.html.get();
                            this.$emit('update:modelValue', html);
                        }
                    },
                },
            };
        },
    },

    watch: {
        modelValue(newVal) {
            this.setEditorContent(newVal);
        },
    },

    methods: {
        getEditorInstance() {
            return this.$refs.froala?.getEditor?.();
        },

        setEditorContent(value) {
            this.$nextTick(() => {
                const editor = this.getEditorInstance();
                if (editor) {
                    const current = editor.html.get();
                    if (current !== value) {
                        editor.html.set(value || '');
                    }
                }
            });
        },
    },
};
</script>
