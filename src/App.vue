<template>
  <div class="container">
    <h1 class="text-2xl mb-6 mt-6">Product Importer</h1>
    <div class="bg-white">
      <nav class="flex flex-col sm:flex-row">
        <button
          v-on:click="changePage('products')"
          class="tab hover:text-blue-500 focus:outline-none"
          :class="{ active: currentPage === 'products' }"
        >
          Products
        </button>
        <button
          v-on:click="changePage('jobs')"
          class="tab hover:text-blue-500 focus:outline-none"
          :class="{ active: currentPage === 'jobs' }"
        >
          Jobs
        </button>
        <button
          v-on:click="changePage('data-sources')"
          class="tab hover:text-blue-500 focus:outline-none"
          :class="{ active: currentPage === 'data-sources' }"
        >
          Data Sources
        </button>
      </nav>
      <div class="p-5">
        <data-sources v-if="currentPage === 'data-sources'"></data-sources>
        <div v-if="currentPage === 'products'">
          <h1>PRODUCTS</h1>
        </div>
        <div v-if="currentPage === 'jobs'">
          <h1>JOBS</h1>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import DataSources from "./Pages/DataSources.vue";

export default {
  components: {
    DataSources,
  },
  data() {
    return {
      currentPage: "products"
    };
  },
  methods: {
    changePage(page) {
      this.currentPage = page;
    },
  },
};
</script>
