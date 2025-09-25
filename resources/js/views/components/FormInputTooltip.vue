<template>
  <div class="form-group" :class="{ 'col-md-6': columnWidth }">
    <label class="form-label" v-if="label">{{ label }}</label>
    <div class="input-group position-relative">
      <input
          :type="type"
          v-model="inputData"
          :placeholder="placeholder ?? label"
          class="form-control"
          :class="{ 'is-invalid': error }"
          @input="$emit('update:modelValue', $event.target.value)"
      />
      <div v-if="error" class="d-block invalid-tooltip">
        {{ error }}
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    modelValue:[String,Object],
    label: {
      required:true,
      type: String,
    },
    placeholder: String,
    error: String,
    type: {
      type: String,
      default: 'text'
    },
    columnWidth: {
      type: Boolean,
      default: true
    }
  },
  computed: {
    inputData: {
      get() {
        return this.modelValue;
      },
      set(value) {
        this.$emit('update:modelValue', value);
      }
    }
  }
};
</script>
