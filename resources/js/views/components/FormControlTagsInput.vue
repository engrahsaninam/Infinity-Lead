<template>
  <div :class="[colClass, colMdClass]">
    <div class="form-group">
      <div :class="{'row align-items-center':layout === 'v'}">
        <div :class="{'col-md-1':layout === 'v'}">
          <div class="d-flex justify-content-between">
            <label :for="id" v-if="label" class="form-label">{{ label }}</label>
          </div>
        </div>
        <div :class="{'col-md-11':layout === 'v'}">
          <multiselect
              :show-no-options="false"
              v-model="selectedTags"
              :id="id"
              :class="{ 'is-invalid': error }"
              :placeholder="placeholderText"
              :multiple="true"
              :taggable="true"
              :close-on-select="false"
              :options="[]"
              @tag="addTag"
          />
        </div>
      </div>


      <div v-if="error" class="invalid-feedback">
        {{ error }}
      </div>
    </div>
  </div>
</template>

<script>
import Multiselect from "vue-multiselect";

export default {
  name: "FormControlTagsInput",
  components: {Multiselect},
  props: {
    modelValue: {
      type: Array, // Ensure it's always an array
      default: () => [],
    },
    layout: {
      type: String,
      required:false,
    },
    show_placeholder: {
      type: Boolean,
      default: true,
      required: false
    },
    error: {
      type: String,
      default: ''
    },
    label: {
      type: String,
      required: true
    },
    id: {
      type: String,
      required: false
    },
    col_md: {
      type: Number,
      default: 6
    },
    col: {
      type: Number,
      default: 12
    }
  },
  computed: {
    colClass() {
      return `col-${this.col}`;
    },
    colMdClass() {
      return `col-md-${this.col_md}`;
    },
    selectedTags: {
      get() {
        return this.modelValue || [];
      },
      set(tags) {
        this.$emit('update:modelValue', tags);
      }
    },
    placeholderText() {
      return this.show_placeholder ? `${this.label || '-- Select an option'}` : '';
    }
  },
  methods: {
    addTag(newTag) {
      if (!this.selectedTags.includes(newTag.trim()) && newTag.trim() !== "") {
        this.$emit('update:modelValue', [...this.selectedTags, newTag.trim()]);
      }
    },
  }
};
</script>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>
