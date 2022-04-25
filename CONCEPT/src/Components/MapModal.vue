<template>
  <div class="modal-parent" v-if="isOpen">
    <div class="modal-window">
      <div class="modal-header">
        <p class="font-semibold text-gray-800">Map Product</p>
        <button v-on:click="onClickButton()">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="modal-body">
        <table class="min-w-full leading-normal">
          <thead>
            <tr>
              <th class="table_heading">WooCommerce Field</th>
              <th class="table_heading">=</th>
              <th class="table_heading">Data Source Field</th>
              <th class="table_heading">
                <button
                  class="px-4 py-2 text-white font-semibold bg-blue-500 rounded"
                  @click="createEmptyMapping()"
                >
                  +
                </button>
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="mappedField in mappedFields" v-bind:key="mappedField.id">
              <td class="table-cell">
                <p
                  class="text-gray-900 whitespace-no-wrap"
                  v-if="mappedField.wcField && edittingField != mappedField.id"
                >
                  {{ mappedField.wcField }}
                </p>
                <select v-else-if="edittingField == mappedField.id" v-model="mappedField.wcField">
                  <option
                    v-for="defaultField in defaultFields"
                    v-bind:key="defaultField.id"
                    :value="defaultField.id"
                  >
                    {{ defaultField.label }}
                  </option>
                </select>
              </td>
              <td class="table-cell">
                <div class="flex items-center">
                  <div class="ml-3">
                    <p class="text-gray-900 whitespace-no-wrap font-bold">=</p>
                  </div>
                </div>
              </td>
              <td class="table-cell">
                <!-- <p class="text-gray-900 whitespace-no-wrap">
                </p>  -->
                <select v-model="mappedField.dsField">
                  <option
                    v-for="defaultField in availableProductFields"
                    v-bind:key="defaultField"
                    :value="defaultField"
                  >
                    {{ defaultField }}
                  </option>
                </select> 
              </td>
              <td class="table-cell">
                <div class="actionBar flex">
                  <div v-if="edittingField != mappedField.id">
                    <button class="mr-4" @click="editField(mappedField.id)">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button class="mr-4">
                      <i class="far fa-trash-alt"></i>
                    </button>
                  </div>
                  <div v-else-if="edittingField == mappedField.id">
                    <button class="mr-4" @click="saveField(edittingField, {wcField: mappedField.wcField, dsField: mappedField.dsField})">
                      <i class="fas fa-check"></i>
                    </button>
                    <button class="mr-4" @click="stopEdit()">
                      <i class="fas fa-times"></i>
                    </button>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <p class="font-semibold text-gray-600">Cancel</p>
        <button class="px-4 py-2 text-white font-semibold bg-blue-500 rounded">
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
      modalOpen: false,
      requiredWCFields: [
        "wc_name",
        "wc_sku"
      ],
      availableProductFields: [],
      defaultFields: [
        {
          id: "wc_name",
          label: "Name",
        },
        {
          id: "wc_regular_price",
          label: "Regular Price",
        },
        {
          id: "wc_description",
          label: "Description",
        },
           {
          id: "wc_sku",
          label: "SKU",
        }
      ],
      exampleProduct: {
        EAN_Code: 8714713038152,
        Artikelcode: "365554-GBS",
        Assortiment: "Kids",
        Productgroep: "Bedden",
        Range: "Dennis",
        Merk: "WOOOD",
        Productnaam_NL: "Dennis Nachtkastje Grenen Steel Grey Geborsteld [fsc]",
        Productnaam_EN: "Dennis Nightstand Steel Grey Brushed [fsc]",
        Productnaam_DE: "Dennis Nachttisch Stahlgrau [fsc]",
        Productnaam_FR: "Dennis Table De Chevet Pin Brosse Anthracite [fsc]",
        Omschrijving_NL: "Geborsteld grenen nachtkastje met lade. Het nachtkastje is gemaakt van grenen en is gespoten in een de stoere kleur staalgrijs. Dit nachtkastje is onderdeel van de kamer Dennis, deze kamer bestaat uit een bedbank of een bed zonder wanden, een buro, een kast, een opbergkist, een nachtkastje en een wandplank. GEBORSTELD HOUT Dit meubel is gemaakt van geborsteld hout. De nerven en noesten worden door het borstelen geaccentueerd waardoor het hout een doorleefde uitstraling krijgt. Door deze bewerking kunnen oneffenheden, kleine scheurtjes en noesten extra zichtbaar zijn. Dit zijn kenmerken van het doorleefde hout. Ieder exemplaar is hierdoor uniek! Dit nachtkastje wordt geleverd als eenvoudig bouwpakket met een duidelijke montage-instructie.",
        Omschrijving_EN: "Brushed pine bedside table with drawer. The bedside table is made of pine and is sprayed in a sturdy color steel grey. This bedside table is part of the Dennis room, this room consists of a sofa bed or a bed without walls, a desk, a closet, a storage box, a bedside table and a wall shelf. This piece of furniture is made of brushed wood. The veins and knots are accentuated by brushing, giving the wood a lived look. This treatment can make unevenness, small cracks and knots extra noticeable. These are characteristics of the lived wood. Each item is unique because of this! This bedside cabinet is supplied as a simple kit with clear assembly instructions.",
        Omschrijving_DE: "",
        Omschrijving_FR: "Table de chevet pin brossé avec tiroir. La table de chevet est en pin et est peinte dans la couleur grise acier dure. Cette table de chevet fait partie de la chambre Dennis, cette chambre se compose d’un canapé ou un lit sans murs, un bureau, une armoire, un coffre de rangement, table de chevet et étagère murale. Ce meuble est en bois brossé et laisse donc apparaitre des petites lignes propre au style. Cela fait de cette armoire une pièce unique ! Cet article est livré dans un paquet d’assemblage simple et avec un guide d’instructions de montage clair",
        Materiaal: "Grenen (geborsteld)",
        Kleur: "Steel Grey",
        Afmetingen: "52x39x36",
        FSC: true,
        Verkoop_eenheid: "ST/PCS",
        "Inkoopprijs_(excl._BTW)": 61.5,
        "Adv._consumentenprijs_(incl_BTW)": 119,
        Status: "Collection",
        New_arrival: false,
        Aantal_pakketten: 1,
        Besteleenheid: 1,
        Vrije_voorraad: 50,
        Montage_vereist: true,
        Land_van_herkomst: "NL",
        Intrastat_code: "94035000",
        Afbeeldingen: {
          image: [
            "https://deeekhoorn.com/assets/PRODUCT_IMAGES/8714713038152.jpg",
            "https://deeekhoorn.com/assets/PRODUCT_IMAGES/8714713038152_2.jpg",
            "https://deeekhoorn.com/assets/PRODUCT_IMAGES/8714713038152_3.jpg",
            "https://deeekhoorn.com/assets/PRODUCT_IMAGES/8714713038152_4.jpg",
            "https://deeekhoorn.com/assets/PRODUCT_IMAGES/8714713038152_5.jpg"
          ]
        }
      },
      parameters: {},
      mappedFields: [],
      edittingField: null
    };
  },
  methods: {
    onClickButton(event) {
      this.$emit("clicked", "someValue");
    },
    createEmptyMapping() {
      // If already editing field, Don't try to create a new mapping pair.
      if(this.edittingField) {
        return;
      }

      // Temporary ID.
      const temporaryId = Math.floor(Math.random() * 1000001);
      this.mappedFields.push({
        id: temporaryId
      });

      // Set the current editing field.
      this.edittingField = temporaryId;
    },
    editField(id) {
      this.edittingField = id;
    },
    saveField(id, params) {
      this.edittingField = null;
    },
    stopEdit(newField = false) {
      this.edittingField = null;
    }
  },
  mounted() {
    this.availableProductFields = Object.keys(this.exampleProduct);
  },
};
</script>

<style>
 .modal-parent {
   @apply flex fixed left-0 top-10 justify-center items-center antialiased z-50
 }
 .modal-window {
   @apply flex flex-col w-2/3 mx-auto rounded-lg border border-gray-300 shadow-xl
 }
 .modal-header {
   @apply flex flex-row justify-between p-6 bg-white border-b border-gray-200 rounded-tl-lg rounded-tr-lg
 }
 .modal-body {
   @apply flex flex-col px-6 py-5 bg-gray-50 h-96
 }
 .modal-footer {
    @apply flex flex-row items-center justify-between p-5 bg-white border-t border-gray-200 rounded-bl-lg rounded-br-lg
 }
 .table-cell {
   @apply px-5 py-5 border-b border-gray-200 bg-white text-sm
 }
</style>