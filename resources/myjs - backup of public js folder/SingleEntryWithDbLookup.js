/**
 * SingleEntryWithDbLookup.js
 * 
 * Entry of a model (eg facilitator) with a loookup from the database
 * - model must exist in the database
 * - 'noform' means that this component must wrapped in a form to be submitted. There is no 'form' control in this component
 * 
 * Properties that must be passed from the calling html
 * model: eg model="course", or model="member"
 * ajaxUurl: the url for making the ajax call eg "{{ url('coursesearch') }}". Required.
 * searchParameterKey: the key to use in the search string eg name=greg+vinall. The default is 'name'
 * setFocus: if exists the autofocus will be set to this field
 * name: the name of this control, which is passed in the request variable along with it's value (eg venues[1])
 * modelDefaults; the default 'id' and 'name' of the model
 */
Vue.component('SingleEntryWithDbLookup',{
   props: {
     model: {
       type: String,
       required: true
     },
     modelDefaults: {
       type: Object,
       required: true
     },
     ajaxUrl: {
       type: String,
       required: true
     },
     searchParameterKey: {
        type: String,
        default: 'name'
     },
     setFocus: {
       type: Boolean,
       default: false
     },
     name: {
       type: String,
       required: true
     }
   },
   template: `<div>
            <div class="row">
              <div class="col-1">
                  <label>{{config}}:</label>
              </div>
              <div class="col-4">
                 <input
                    type="text"
                    class="w-100"
                    v-bind:placeholder="prompt"
                    v-bind:autofocus="setFocus"
                    ref="toSearchFor"
                    v-model="toSearchFor"
                    value=""
                />
                <input
                    type="hidden"
                    v-bind:name="name"
                    v-model="toSearchForId"
                    value=""
                />
              </div>
              <div class="col-1">
                <button
                    type="button"
                    class="btn btn-outline-secondary btn-sm disabled ml-n4"
                    v-on:click="clear">
                X
                </button>
              </div>
            </div>
            <div class="row"> 
              <div class="col-1"> </div>
              <div class="col-4">
                <select
                    v-model="selectedtoSearchFor"
                    v-if="showMatches"
                    size="6"
                    v-on:change="showSelected()">
                        <option disabled value="">
                            If the name is in the list, please select it...
                        </option>
                        <option
                            v-for="APIresult in APIresults"
                            v-bind:value="APIresult">
                              {{ displayProperty(APIresult) }}
                        </option>
                </select>
              </div>
            </div>
      </div>`,
   data: function() {
      return {
         config: '',
         toSearchFor: '', // name - linked to input
         toSearchForId: '', // id - linked to hidden id input
         prompt: '',
         APIresults: [], // return result from API call.
         selectedtoSearchFor: '', // linked to the value of the selected option in the dropdown (an APIresult)
         showMatches: false,
         doSearch: true
      }
   },
   computed: {
     axiosParams() {
       const params = new URLSearchParams()
       params.append(this.searchParameterKey, this.toSearchFor)
       return params
     },
   },
   watch: {
     // whenever toSearchFor changes, this function will run
     toSearchFor: function (newtoSearchFor, oldtoSearchFor) {
       this.debouncedGettoSearchFor()
     }
   },
   created: function () {
     // Limit how often we access the api, waiting until the user has completely
     // finished typing before making the ajax request.
     this.debouncedGettoSearchFor = _.debounce(this.gettoSearchFor, 500)
     this.config = _.capitalize(this.model)
     this.setPrompt()
     this.toSearchFor = this.modelDefaults.name
     this.toSearchForId = this.modelDefaults.id
   },
   methods: {
     clear: function (event) {
        this.toSearchFors = []
        this.selectedtoSearchFor = ''
        this.showMatches = false
        this.toSearchFor = ''
        this.toSearchForId = ''
        this.$refs.toSearchFor.focus()
     },
     showSelected: function() {
       this.doSearch = false
       this.toSearchFor = this.selectedtoSearchFor.name
       this.toSearchForId = this.selectedtoSearchFor.id
       this.showMatches = false
       this.$refs.toSearchFor.focus()
       this.doSearch = true
     },
     setPrompt: function() {
       this.prompt = 'Enter ' + _.toLower(this.model) + ' name, or part thereof'
     },
     gettoSearchFor: function () {
       this.setPrompt();
       if (this.toSearchFor === '') {
         this.showMatches = false
       } else {
         var sc = this
         axios.defaults.url = this.ajaxUrl
         axios({
           method: 'get',
           responseType: 'json',
           params: sc.axiosParams,
           timeout: 2000
         })
           .then(function (response) {
             if (response.data.length === 0) {
               sc.showMatches = false
             } else {
               sc.APIresults = response.data
               if (_.toLower(sc.APIresults[0].name) === _.toLower(sc.toSearchFor)) {
                 sc.showMatches = false
                 sc.toSearchForId = sc.APIresults[0].id
               } else {
                 sc.showMatches = true
               }
             }
           })
           .catch(function (error) {
             sc.prompt = 'Error! Unable to retrieve matches. '
           })
         }
       },
     displayProperty(theSelectedObject) {
       switch(this.config) {
         case 'Address': return theSelectedObject.address; break;
         default: return theSelectedObject.name;
       }
     }
   }
});