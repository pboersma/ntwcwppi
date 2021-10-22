<template>
  <div class="flex flex-col justify-between sm:flex-row">
    <h2 class="text-3xl font-semibold">Data Sources</h2>
  </div>
  <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
    <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
      <table class="min-w-full leading-normal">
        <thead>
          <tr>
            <th class="table_heading">Source</th>
            <th class="table_heading">Authentication</th>
            <th class="table_heading">URL</th>
            <th class="table_heading">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="dataSource in dataSources" v-bind:key="dataSource.name">
            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
              <div class="flex items-center">
                <div class="ml-3">
                  <p class="text-gray-900 whitespace-no-wrap">
                    {{ dataSource.name }}
                  </p>
                </div>
              </div>
            </td>
            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
              <p class="text-gray-900 whitespace-no-wrap">
                {{ dataSource.authentication }}
              </p>
            </td>
            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
              <p class="text-gray-900 whitespace-no-wrap">
                {{ dataSource.source }}
              </p>
            </td>
            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
              <button v-on:click="openModal()" class="flex font-bold mr-6">
                <i class="fas fa-sync"></i>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <sync-modal
      class="fixed w-full"
      @clicked="closeModal()"
      :isOpen="modalOpen"
    ></sync-modal>
  </div>
</template>
<script>
import SyncModal from '../Components/SyncModal.vue';
export default {
  components: {
    SyncModal
  },
  data() {
    return {
      modalOpen: false,
      dataSources: [
        {
          name: "De Eekhoorn",
          source: "https://deeekhoorn.com/nl/feed/products/json",
          authentication: true
        },
      ],
    };
  },
  methods: {
    openModal() {
      this.modalOpen = true;
    },
    closeModal() {
      this.modalOpen = false;
    },
  },
};
</script>