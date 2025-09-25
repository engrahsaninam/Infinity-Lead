<template>
    <div class="position-relative froala-editor">
        <!-- Editor -->
        <froala ref="froala" :tag="'textarea'" :config="FConfig" />
        <div class=" row justify-content-end mt-2">
            <div class="col-md-6 d-flex flex-column justify-content-center">
                <p class="text-muted m-0 small">
                    <span v-if="showCounter">
                        {{ wordCount }}/100 words.
                        <span v-if="showCounter && showSpam">
                            |
                        </span>
                    </span>
                    <span v-if="showSpam">
                        Words highlighted in orange are spam trigger words.
                        <a class="text-primary cursor-pointer font-weight-bold" @click="highlightSpamWords">Check
                            now</a>
                    </span>
                </p>

            </div>

            <div class="col-md-2 d-flex align-items-start justify-content-end" v-if="showAI">
                <div class="switch mt-2">
                    <input type="checkbox" id="AI" v-model="aiSwitch" />
                    <label for="AI"></label>
                </div>
                <button class="chatgpt-btn ml-2" v-if="aiSwitch" @click="aiColorize">
                    <img src="../../assets/img/chatgpt.png" alt="" width="20" height="20" />
                </button>
            </div>

            <div class="col-md-4 d-flex align-items-start" v-if="showFieldsDropdown">
                <FormControlSelect2 :col_md="12" :options="headerOptions" v-model="selectedHeader"
                    label="{ } Add Custom Variable" :show_label="false" @select="insertHeaderInEditor" />
            </div>
        </div>
    </div>
</template>

<script>
import FormControlSelect2 from "./FormControlSelect2.vue";
import { notify, NOTIFY_TYPE_ERROR, SPAM_WORDS } from "../../constants.js";

export default {
    name: "TipTapEditor",
    components: { FormControlSelect2 },
    props: {
        modelValue: { type: String, default: "" },
        config: { type: Object, default: () => ({}) },
        headerOptions: { type: Array, default: () => [] },
        showCounter: { type: Boolean, default: true },
        showSpam: { type: Boolean, default: true },
        showAI: { type: Boolean, default: true },
        showFieldsDropdown: { type: Boolean, default: true },
    },

    data() {
        return {
            aiSwitch: false,
            selectedHeader: null,
            wordCount: 0,
            spamWords: SPAM_WORDS,
        };
    },

    computed: {
        FConfig() {
            return {
                placeholderText: "Write text here...",
                charCounterCount: true,
                heightMin: 300,
                toolbarButtons: [
                    "bold", "italic", "underline", "strikeThrough",
                    "subscript", "superscript",
                    "undo", "redo",
                    "fontFamily", "fontSize",
                    "color", "backgroundColor",
                    "align", "formatOL", "formatUL", "outdent", "indent", "quote",
                    "insertLink", "insertTable",
                    "emoticons", "specialCharacters", "insertHR", "selectAll", "clearFormatting", "html"
                ],
                events: {
                    initialized: () => {
                        this.setEditorContent(this.modelValue);
                    },
                    blur: () => {
                        this.onEditorBlur();
                    },
                    contentChanged: () => {
                        const editor = this.getEditorInstance();
                        if (editor) {
                            const html = editor.html.get();
                            this.updateWordCount(html);
                            this.$emit("update:modelValue", html);
                        }
                    },
                },
                ...this.config,
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
                        editor.html.set(value || "");
                    }
                }
            });
        },

        updateWordCount(html) {
            const text = html.replace(/<[^>]*>/g, " "); // Remove HTML tags
            this.wordCount = text.trim().split(/\s+/).filter(Boolean).length;
        },

        highlightSpamWords() {
            const editor = this.getEditorInstance();
            if (!editor) return;

            const text = editor.$el.text();
            let foundWords = [];

            this.spamWords.forEach((word) => {
                const escaped = word.replace(/[-/\\^$*+?.()|[\]{}]/g, "\\$&");
                const regex = new RegExp(`\\b${escaped}\\b`, "gi");
                let match;
                while ((match = regex.exec(text)) !== null) {
                    foundWords.push(match[0]);
                }
            });

            if (foundWords.length > 0) {
                // Highlight each found word in editor
                let html = editor.html.get();
                foundWords.forEach((word) => {
                    const escaped = word.replace(/[-/\\^$*+?.()|[\]{}]/g, "\\$&");
                    const regex = new RegExp(`(\\b${escaped}\\b)(?![^<]*>)`, "gi");
                    html = html.replace(regex, `<span style="background-color: orange;">$1</span>`);
                });
                editor.html.set(html);
                notify(`Spam words (${foundWords.length}): ` + foundWords.join(", "), { autoClose: 3000 }, NOTIFY_TYPE_ERROR);
            } else {
                notify("No spam words found");
            }
        },

        aiColorize() {
            const editor = this.getEditorInstance();
            if (!editor) return;
            const selectedText = editor.selection.text();
            if (selectedText && selectedText.trim().length > 0) {
                editor.html.insert(`<span style="color: purple;">${selectedText}</span>`);
            } else {
                notify("Please select some text to highlight!", { autoClose: 3000 }, NOTIFY_TYPE_ERROR);
            }
        },

        insertHeaderInEditor() {
            const editor = this.getEditorInstance();
            if (!this.selectedHeader || !editor) return;

            const variable = `{${this.selectedHeader}}`;

            // Restore selection before insert
            editor.selection.restore();
            editor.events.focus(true); // Ensure editor is active

            const selectedText = editor.selection.text();

            if (selectedText && selectedText.trim().length > 0) {
                // Insert selected text + variable manually
                editor.html.insert(`${selectedText}${variable}`);
            } else {
                // Just insert variable at current position
                editor.html.insert(variable);
            }

            // Optional: clear dropdown selection after insert
            this.selectedHeader = null;
        },
        onEditorBlur() {
            const editor = this.getEditorInstance();
            if (editor) {
                editor.selection.save();
            }
        }
    },
};
</script>
<style>
.chatgpt-btn {
    background: #f1f1f1;
    border: none;
    padding: 6px;
    border-radius: 4px;
}
#fr-logo{
    display: none;
}
.fr-box.fr-basic .fr-element{
    line-height: 1!important;
}
.fr-second-toolbar{
    height: 25px;
}.fr-box.fr-basic .fr-wrapper
{
    border-bottom: 0!important;
}
.fr-wrapper>div:first-child>a[href*="froala.com"] {
    display: none !important;
}
</style>

