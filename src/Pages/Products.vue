<template>
  <div class="flex flex-col justify-between sm:flex-row">
    <h2 class="text-3xl font-semibold">Products</h2>
  </div>
  <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
    <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
      <table class="min-w-full leading-normal">
        <thead>
          <tr>
            <th class="table_heading">Name</th>
            <th class="table_heading">Inkoopprijs (excl.BTW)</th>
            <th class="table_heading">EAN Code</th>
            <th class="table_heading">Status</th>
            <th class="table_heading">Synced</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="product in products" v-bind:key="product.EAN_Code">
            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
              <div class="flex items-center">
                <div class="ml-3">
                  <p class="text-gray-900 whitespace-no-wrap">
                    {{ product.Productnaam_NL }}
                  </p>
                </div>
              </div>
            </td>
            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
              <p class="text-gray-900 whitespace-no-wrap">
                {{ product["Inkoopprijs_(excl._BTW)"] }}
              </p>
            </td>
            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
              <p class="text-gray-900 whitespace-no-wrap">
                {{ product.EAN_Code }}
              </p>
            </td>
            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                <p class="text-gray-900 whitespace-no-wrap">
                    {{ product.Status }}
                </p>
            </td>
            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
              <p class="text-gray-900 whitespace-no-wrap">
                {{ product.data_source_id }}
              </p>
            </td>
          </tr>
        </tbody>
      </table>
       <div class="px-5 py-5 bg-white border-t flex flex-col xs:flex-row items-center xs:justify-between">
          <span class="text-xs xs:text-sm text-gray-900">
              Showing Page {{currentPage}} of {{totalPages}} 
          </span>
          <div class="inline-flex mt-2 xs:mt-0">
              <button
                  v-on:click="getProducts('previous')"
                  class="text-sm bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-l">
                  Prev
              </button>
              <button
                  v-on:click="getProducts('next')"
                  :disabled="disableActions"
                  class="text-sm bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-r">
                  Next
              </button>
          </div>
        </div>
    </div>
  </div>
</template>
<script>
import axios from "axios";
export default {
  data() {
    return {
      products: [],
      productListLimit: 10,
      currentPage: 1,
      totalPages: null,
      disableActions: false,
    };
  },
  mounted() {
    // Get Initial Product Data Set
    this.getProducts();
  },
  methods: {
    getProducts(action = 'none'){
      const self = this;

      // Disable Actions to give call a chance of finishing before next input.
      self.disableActions = true;

      if(action !== 'none'){
        switch(action){
          case "previous":
            if(self.currentPage <= 1) {
              break;
            }
            self.currentPage--
            break;
          case "next":
            if(self.currentPage >= self.totalPages) {
              break;
            }
            self.currentPage++
            break;
          default:
            break;
        }
      }

      axios
        .get("https://lycan-media.nl/wp-json/ntwcwppi/v1/products/list", {
          params: {
            productListLimit: self.productListLimit,
            page: self.currentPage
          },
        })
        .then(function (response) {
          self.totalPages = response.data.totalPages;

          // Empty Product List before Insertion.
          self.products = [];

          response.data.products.map(function (value, key) {
            let productData = JSON.parse(value.data);
            // Set Data Source ID in Data of Product.
            productData["data_source_id"] = value.data_source_id;

            self.products.push(productData);
          });
          
          // Re-enable actions after call is done.
          self.disableActions = false;
        })
        .catch(function (error) {
          console.log(error);
        })
        .then(function () {});
    }
  }
};
</script>