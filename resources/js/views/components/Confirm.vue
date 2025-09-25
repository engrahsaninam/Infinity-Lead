<template>
  <div v-if="show" class="popup-overlay">
    <div class="popup">
      <h1><i class="pi pi-exclamation-circle text-warning" style="font-size: 75px;padding-bottom: 30px;"></i></h1>
      <h2 class="popup-heading">{{ heading }}</h2>
      <p class="popup-text">{{ message }}</p>
      <div class="popup-actions">
        <button class="btn btn-light btn-cancel border" @click="$emit('cancel')">Cancel</button>
        <button class="btn btn-danger btn-confirm" @click="$emit('confirm')">Confirm</button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name:'Confirm',
  props: {
    show: Boolean,
    heading: String,
    message: String,
  },
  emits: ['confirm', 'cancel'],
};
</script>

<style scoped>
.popup-overlay {
  z-index: 9999999;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
}
.popup {
  background: white;
  padding: 50px;
  border-radius: 8px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
  text-align: center;
  width: 400px;
}
.popup-heading {
  font-size: 20px;
  margin-bottom: 10px;
}
.popup-text {
  font-size: 16px;
  margin-bottom: 20px;
}
.popup-actions {
  display: flex;
  justify-content: space-between;
}
.btn {
  padding: 8px 16px;
  border: none;
  cursor: pointer;
  border-radius: 4px;
}

</style>
