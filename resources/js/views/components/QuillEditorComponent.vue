<template>
  <div class="position-relative">
    <span class="toggleBtn  m-0 small pl-3 cursor-pointer position-absolute" @click="toggleHtml">
      <span v-if="!showHtml"><i class="pi pi-code"></i></span>
      <span v-else class=""><i class="pi pi-undo"></i></span>
    </span>
    <div v-show="!showHtml">
      <QuillEditor
          theme="snow"
          v-model:content="inputData"
          content-type="html"
          ref="quillEditor"
          @ready="onEditorReady"
          :options="quillOptions"
          class="border-0 quill-editor"
          @update:content="updateContent"
          @blur="onBlurFunction"
      />
    </div>
    <div v-show="showHtml" class="pt-5">
      <textarea v-model="htmlContent" class="form-control border-0" rows="10" @change="applyHtmlToEditor"></textarea>
    </div>

    <!-- Footer with Additional Options -->
    <div class="row justify-content-end">
      <div class="col-md-6">
        <div class="d-flex">

          <p class="text-muted m-0 small pl-3 " v-if="show_counter">{{ wordCount }}/100 words</p>
        </div>
        <p class="text-muted m-0 small pl-3" v-if="show_spam">
          Words highlighted in orange are spam trigger words.
          <a class="text-primary cursor-pointer font-weight-bold" @click="highlightSpamWords">Check now</a>
        </p>
      </div>
      <div class="col-md-2 d-flex align-items-start justify-content-end" v-if="show_ai">
        <div class="switch mt-2">
          <input
              type="checkbox"
              id="AI"
              v-model="ai_switch"
              value="1"
          />
          <label :for="'AI'"></label>
        </div>
        <button class="chatgpt-btn" v-if="ai_switch" @click="ai_colorize">
          <img src="../../assets/img/chatgpt.png" alt="" width="20" height="20">
        </button>
      </div>
      <div class="col-md-4 d-flex align-items-start" v-if="show_fields_dropdown">
        <FormControlSelect2
            :col_md="12"
            :options="headerOptions"
            v-model="selectedHeader"
            label="{ } Add Custom Variable"
            :show_label="false"
            @select="insertHeaderInQuillEditor"
        />
      </div>
    </div>
  </div>
</template>

<script>
import FormControlSelect2 from "./FormControlSelect2.vue";
import { notify, NOTIFY_TYPE_ERROR, SPAM_WORDS } from "../../constants.js";

export default {
  components: { FormControlSelect2 },
  props: {
    modelValue: String,
    headerOptions: Array,
    show_counter: {
      type: Boolean,
      required: false,
      default: true,
    },
    show_spam: {
      type: Boolean,
      required: false,
      default: true,
    },
    show_ai: {
      type: Boolean,
      required: false,
      default: true,
    },
    show_fields_dropdown: {
      type: Boolean,
      required: false,
      default: true,
    },
    onBlur: {
      type: Function,
      required: false,
      default: null
    }
  },
  data() {
    return {
      ai_switch: false,
      spamWords: SPAM_WORDS,
      quillOptions: {
        modules: {
          toolbar: [
            [{ 'header': '1' }, { 'header': '2' }],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            ['bold', 'italic', 'underline'],
            [{ 'align': [] }],
            ['link'],
            [{ 'color': [] }, { 'background': [] }],
            [{ 'script': 'sub'}, { 'script': 'super' }],
            ['blockquote', 'code-block'],
            ['clean'],
          ],
        },
        formats: [
          'header',
          'list',
          'bold', 'italic', 'underline',
          'align',
          'link',
          'image',
          'color', 'background',
          'script',
          'blockquote', 'code-block',
          'clean',
        ],
        placeholder: "Type your message here...",
      },
      wordCount: 0,
      selectedHeader: null,

      showHtml: false,
      htmlContent: "",
    };
  },
  methods: {
    toggleHtml() {
      const quill = this.$refs.quillEditor.getQuill();
      if (!this.showHtml) {
        this.htmlContent = quill.root.innerHTML;
        this.showHtml = true;
      } else {
        this.applyHtmlToEditor();
        this.showHtml = false;
      }
    },

    applyHtmlToEditor() {
      const quill = this.$refs.quillEditor.getQuill();
      if (quill && this.htmlContent !== null) {
        quill.root.innerHTML = this.htmlContent;
        this.updateContent(this.htmlContent);
      }
    },

    onEditorReady(editor) {
      this.$refs.quillEditor = editor;
    },
    updateContent(content) {
      this.wordCount = content ? content.split(/\s+/).filter(Boolean).length : 0;
      this.$emit("update:modelValue", content);
    },

    insertHeaderInQuillEditor() {
      const quill = this.$refs.quillEditor.getQuill();
      if (!this.selectedHeader) return;

      const variable = `{${this.selectedHeader}}`;
      let range = quill.getSelection();

      if (range) {
        quill.insertText(range.index, variable);
      } else {
        quill.insertText(quill.getLength(), variable);
      }
    },

    highlightSpamWords() {
      const quill = this.$refs.quillEditor.getQuill();
      const text = quill.getText();
      let foundWords = [];

      this.spamWords.forEach((word) => {
        const escapedWord = word.replace(/[-/\\^$*+?.()|[\]{}]/g, "\\$&");
        const regex = new RegExp(`\\b${escapedWord}\\b`, "gi");
        let match;

        while ((match = regex.exec(text)) !== null) {
          let startIndex = match.index;
          let length = match[0].length;
          quill.formatText(startIndex, length, "background", "orange");
          if (!foundWords.includes(match[0])) {
            foundWords.push(match[0]);
          }
        }
      });

      if (foundWords.length > 0) {
        notify('Spam words ('+foundWords.length+'): ' + foundWords, { autoClose: 3000 }, NOTIFY_TYPE_ERROR);
      } else {
        notify('No spam words found');
      }
    },

    ai_colorize() {
      const quill = this.$refs.quillEditor.getQuill();
      let range = quill.getSelection();
      if (range && range.length > 0) {
        const currentColor = quill.getFormat(range.index, range.length).color;
        if (currentColor === "purple") {
          quill.formatText(range.index, range.length, "color", false); // This removes the color
        } else {
          quill.formatText(range.index, range.length, "color", "purple");
        }
      } else {
        notify("Please select some text to highlight!", { autoClose: 3000 }, NOTIFY_TYPE_ERROR);
      }
    },

    onBlurFunction() {
      this.onBlur();
    }
  },
  computed: {
    inputData: {
      get() {
        return this.modelValue;
      },
      set(value) {
        this.$emit("update:modelValue", value);
      },
    },
  },

};
</script>
<style>
.ql-toolbar.ql-snow{
  padding-left: 35px!important;
  border: none!important;
  border-top: 1px solid #efefef!important;
}
.toggleBtn{
  top: 14px;
}
</style>