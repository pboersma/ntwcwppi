<template>
  <div
    class="
      flex
      fixed
      left-0
      top-10
      justify-center
      items-center
      antialiased
      z-50
    "
    v-if="isOpen"
  >
    <div
      class="
        flex flex-col
        w-11/12
        sm:w-5/6
        lg:w-1/2
        max-w-2xl
        mx-auto
        rounded-lg
        border border-gray-300
        shadow-xl
      "
    >
      <div
        class="
          flex flex-row
          justify-between
          p-6
          bg-white
          border-b border-gray-200
          rounded-tl-lg rounded-tr-lg
        "
      >
        <p class="font-semibold text-gray-800">Map Product</p>
        <button v-on:click="onClickButton()">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="flex flex-col px-6 py-5 bg-gray-50 h-96 overflow-y-scroll">
      </div>
      <div
        class="
          flex flex-row
          items-center
          justify-between
          p-5
          bg-white
          border-t border-gray-200
          rounded-bl-lg rounded-br-lg
        "
      >
        <p class="font-semibold text-gray-600">Cancel</p>
        <button
          class="px-4 py-2 text-white font-semibold bg-blue-500 rounded"
        >
          Save
        </button>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  props: ["isOpen"],
  data() {
    return {
      modalOpen: false
    };
  },
  methods: {
    onClickButton(event) {
      this.$emit("clicked", "someValue");
    },
  },
};
</script>