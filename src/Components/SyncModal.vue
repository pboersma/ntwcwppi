<template>
    <div class="flex absolute left-0 top-0 justify-center h-screen items-center antialiased z-50 bg-opacity-50 bg-black" v-if="isOpen">
      <div class="flex flex-col w-11/12 sm:w-5/6 lg:w-1/2 max-w-2xl mx-auto rounded-lg border border-gray-300 shadow-xl">
        <div class="flex flex-row justify-between p-6 bg-white border-b border-gray-200 rounded-tl-lg rounded-tr-lg">
          <p class="font-semibold text-gray-800">Sync Data Source</p>
          <button v-on:click="onClickButton()">
            X
          </button>
        </div>
        <base-progress :percentage="20" class="mx-2 mb-2 h-5">
          <span class="text-xs text-white w-full flex justify-end pr-2">{{20}}%</span>
        </base-progress>
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
import BaseProgress from "./BaseProgress";

export default {
    props: ['isOpen'],
    components: {
      BaseProgress
    },
    data() {
        return {
            currentPage: "products",
            modalOpen: false,
            productQueu: [],
            products: [],
            contentProgress: 20
        };
    },
    methods: {
        onClickButton (event) {
            this.$emit('clicked', 'someValue')
        },
        async syncDatasource () {
          var self = this;
          var lengthOfList = null;

          await this.productQueu.push({id: 1, message: "Starting Sync"}); 

          // // Make a request for a user with a given ID
          await axios.get('https://lycan-media.nl/wp-json/ntwcwppi/v1/datasources/create')
          .then(async function (response) {
            await self.productQueu.push({id: 2, message: "Retrieved Data from Data Source"}); 
            lengthOfList = response.data.lenght;
            self.products = response.data;
          })
          .catch(async function (error) {
            await self.productQueu.push({id: 3, message: "Sync Failed " + error}); 
          });

          await this.productQueu.push({id: 4, message: "Importing Products into NTWCWPPI"})
          
          let currentId = 5;

          this.products.map(async function(value, key){
            await axios.post('https://lycan-media.nl/wp-json/ntwcwppi/v1/products/add', { data_source_id: 1, data: value })
            .then(async function (response) {
              await currentId++
              await self.productQueu.push({id: currentId, message: value.Productnaam_NL})
            })
          })
          // for(product in this.products) {
          //   await console.log(product);
          //   // await axios.post('https://lycan-media.nl/wp-json/ntwcwppi/v1/products/add', { data_source_id: 1, data: product })
          //   // .then(async function (response) {
          //   //   await currentId++
          //   //   await self.productQueu.push({id: currentId, message: product})
          //   // })
          // }

        }
  }
};
</script>