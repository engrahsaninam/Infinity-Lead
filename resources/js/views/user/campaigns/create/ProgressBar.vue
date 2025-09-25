<template>
  <ul class="progressbar">
    <li
        v-for="(label, index) in steps"
        :key="index"
        :class="{ active: index < step }"
    >
      <span class="circle">{{ index + 1 }}</span>
    </li>
  </ul>
</template>

<script>
export default {
  props: {
    step: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      steps: [
        'Step 1', 'Step 2', 'Step 3', 'Step 4', 'Step 5',
        'Step 6', 'Step 7', 'Step 8', 'Step 9', 'Step 10',
        'Step 11', 
      ],
    };
  },
};
</script>

<style scoped>
.progressbar {
  counter-reset: step;
  display: flex;
  justify-content: space-between;
  list-style: none;
  padding: 0;
  margin: 10px 0;
}

.progressbar li {
  position: relative;
  text-align: center;
  flex: 1;
}

.progressbar li::before {
  content: '';
  width: 100%;
  height: 4px;
  background: #ccc;
  position: absolute;
  top: 16px;
  left: -50%;
  z-index: 0;
}

.progressbar li:first-child::before {
  display: none;
}

.progressbar li.active::before {
  background: #0c0381;
}

.circle {
  width: 36px;
  height: 36px;
  display: inline-block;
  line-height: 36px;
  border-radius: 50%;
  background-color: #ccc;
  color: white;
  z-index: 1;
  position: relative;
}

.active .circle {
  background-color: #0c0381;
}

.label {
  display: block;
  margin-top: 8px;
  font-size: 12px;
}
</style>
