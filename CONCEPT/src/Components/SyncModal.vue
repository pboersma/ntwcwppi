<template>
    <div class="flex absolute left-0 top-5 justify-center items-center antialiased z-50" v-if="isOpen">
      <div class="flex flex-col w-11/12 sm:w-5/6 lg:w-1/2 max-w-2xl mx-auto rounded-lg border border-gray-300 shadow-xl">
        <div class="flex flex-row justify-between p-6 bg-white border-b border-gray-200 rounded-tl-lg rounded-tr-lg">
          <p class="font-semibold text-gray-800">Sync Data Source</p>
          <button v-on:click="onClickButton()">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="flex flex-col px-6 py-5 bg-gray-50 h-96 overflow-y-scroll">
          <p  class="font-bold" v-for="product in productQueu" v-bind:key="product.id">{{ product.message }}</p>
        </div>
        <div
          class="flex flex-row items-center justify-between p-5 bg-white border-t border-gray-200 rounded-bl-lg rounded-br-lg"
        >
          <p class="font-semibold text-gray-600">Cancel</p>
          <button class="px-4 py-2 text-white font-semibold bg-blue-500 rounded" v-on:click="syncDatasource()">
            Save
          </button>
        </div>
      </div>
    </div>
</template>
<script>
import axios from 'axios';

export default {
    props: ['isOpen'],
    components: {},
    data() {
        return {
            currentPage: "products",
            modalOpen: false,
            productQueu: [],
            products: [],
        };
    },
    methods: {
        /**
         * Clickbutton Event for closing Modal.
         */
        onClickButton(event) {
          this.$emit("clicked", "someValue");
        },
        /**
         * Sync Datasource.
         *
         * @returns {void}
         */
        async syncDatasource () {
          var self = this;
          let currentId = 0;

          // Start Sync
          this.productQueu.push({id: currentId++, message: "Starting Sync"}); 

          // Make a request for a user with a given ID
          // @TODO: Change URL to datasources/get to get Datasource Credentials & URL
          await axios.get('https://lycan-media.nl/wp-json/ntwcwppi/v1/datasources/create')
          .then(async function (response) {
            self.productQueu.push({id: currentId++, message: "Retrieved Data from Data Source"}); 
            self.products = response.data;
          })
          .catch(async function (error) {
            self.productQueu.push({id: currentId++, message: "Sync Failed " + error}); 
          });

          // @TODO: Check if Products already exist

          // Start Import into WP Database.
          this.productQueu.push({id: currentId++, message: "Importing Products into NTWCWPPI"})

          // Map Product Data & Push mapped products into WP Database
          this.products.map(async function(value, key){
            await axios.post('https://lycan-media.nl/wp-json/ntwcwppi/v1/products/add', { data_source_id: 1, data: value })
            .then(async function (response) {
              currentId++
              self.productQueu.push({id: currentId++, message: value.Productnaam_NL})
            })
          })
        }
  }
};
</script>