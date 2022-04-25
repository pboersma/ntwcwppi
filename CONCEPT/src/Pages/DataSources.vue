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
              <div class="flex">
              <button v-on:click="openModal('sync')" class="font-bold mr-6">
                <i class="fas fa-sync"></i>
              </button>
              <button v-on:click="openModal('map')" class="font-bold mr-6">
                <i class="fas fa-map"></i>
              </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <sync-modal
      class="fixed w-full"
      @clicked="closeModal()"
      :isOpen="( currentModal === 'sync' && modalOpen)"
    ></sync-modal>
    <map-modal
      class="fixed w-full"
      @clicked="closeModal()"
      :isOpen="( currentModal === 'map' && modalOpen)"
    ></map-modal>
  </div>
</template>
<script>
import SyncModal from '../Components/SyncModal.vue';
import MapModal from '../Components/MapModal.vue';

export default {
  components: {
    SyncModal,
    MapModal
  },
  data() {
    return {
      currentModal: null,
      modalOpen: false,
      dataSources: null
    };
  },
  methods: {
    openModal(modal) {
      this.currentModal = modal;
      this.modalOpen = true;
    },
    closeModal() {
      this.currentModal = null;
      this.modalOpen = false;
    },
    async fetchDataSources() {
      await axios.get('https://lycan-media.nl/wp-json/ntwcwppi/v1/datasources')
        .then(response => {
          this.dataSources = response.data;
        })
        .catch(error => {
          console.log(error);
        });
    },
  },
  mounted() {
    // this.dataSources = 
  },
};
</script>