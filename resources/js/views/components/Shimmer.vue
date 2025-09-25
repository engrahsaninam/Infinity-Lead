<template>
  <div :class="`col-${column}`" v-if="onLoading">
    <div class="shimmerBG card" >
      <div :class="`shimmerBG`" :style="{ height: `${height}px` }"></div>
    </div>
  </div>

</template>

<script>
export default {
  props: {
    onLoading:{
      type:Boolean,
      required:true,
    },
    column: {
      type: Number,
      required: true
    },
    height: {
      type: Number,
      default: 100
    }
  }
};
</script>
