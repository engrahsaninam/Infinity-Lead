<template>
    <div class="col-md-12 ">
        <label :for="id" class="form-label">{{ label }}</label>
        <Editor class="form-control" :class="{ 'is-invalid': error }" v-model="inputData"  :id="id" :api-key="TINY_MCE_API_KEY()" :init="{ plugins: TINY_MCE_PLUGIN(), toolbar:TINY_MCE_TOOLBAR() }"/>
        <div v-if="error" class="invalid-feedback">
            {{ error }}
        </div>
    </div>
</template>
<script >

import Editor from "@tinymce/tinymce-vue";
import {TINY_MCE_API_KEY, TINY_MCE_PLUGIN, TINY_MCE_TOOLBAR} from "../../constants.js";

export default {
    name:'TinyMceRichTextEditor',
    methods: {
        TINY_MCE_TOOLBAR() {
            return TINY_MCE_TOOLBAR
        },
        TINY_MCE_PLUGIN() {
            return TINY_MCE_PLUGIN
        },
        TINY_MCE_API_KEY() {
            return localStorage.getItem('mce') || '657i9z05qcrrdh8v67fft35x2vb1po6urescw339pxv98nbw';
        }
    },
    components:{
        Editor
    },
    props:{
        modelValue: String,
        id: {
            type: String,
            required: true
        },

        label: {
            type: String,
            required: true
        },
        error: {
            type: String,
            default: null,
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
}
</script>
