Vue.component('dualentry',{
   props: ['model', 'ajaxurl', 'searchparameterkeys'],
   template: `<div>
    <form v-bind:action="href" autocomplete="off" method="GET">
        <table>
            <tr><td></td></tr u>
            <tr>
                <td><label class="col-xs-3 col-form-label mr-2" for="first_name_input">{{config}}</label></td>
                <td>
                    <input type="text" ref="toSearchFor" autofocus="autofocus" v-model="toSearchFor" size="40" id="toSearchFor_input" class="form-control" name="toSearchFor" value="" />
                </td>
            </tr>

            <tr>
                <td></td>
                <td> 
                    <select id="name_matches" v-model="selectedtoSearchFor" v-if="showMatches" size="6" name="findMatches" class="form-control custom-select" onchange="this.form.submit()">
                        <option disabled value="">If the name is in the list, please select it...</option>
                        <option v-for="toSearchFor in toSearchFors" v-bind:value="toSearchFor.id">{{ displayProperty(toSearchFor) }}</option>
                    </select>
                </td>
            </tr>
            <tr><td></td>
                <td v-if="showPrompt">
                    {{ prompt }}

                </td>
                <td v-else="showPrompt">
                    <input id="clear_names" type="checkbox" v-model="clear" name="clearNames" class="form-check-input ml-3">
                    <label class="form-check-label ml-5" for="clearNames">clear</label>
                </td>
            </tr>
            <tr>
                <td></td>
                    <td v-if="showNewButton">
                        <button type="submit" id="newModel" name="newModel" value="1" class="btn btn-primary btn-sm">New {{config}}</button>
                    </td>
            </tr>
            <tr><td>&nbsp;</td></tr>
        </table>
    </form>
      </div>`,
   data: function() {
      return {
         config: '',
         axiosUrl: 'http://127.0.0.1/~gregvinall/bathurstu3a/db21/index.php/coursesearch',
         axiosParamKey: 'name',
         allowNew: false, // allow New Courses

         toSearchFor: '',
         prompt: '',
         toSearchFors: [],
         selectedtoSearchFor: '',
         showPrompt: true,
         showMatches: false,
         showNewButton: false,
         clear: false,
         href: "http://127.0.0.1/~gregvinall/bathurstu3a/db21/index.php/testPage"
      }
   },
   computed: {
     axiosParams() {
       const params = new URLSearchParams();
       params.append(this.searchparameterkey ?? 'name', this.toSearchFor);
       return params;
     },
   },
   watch: {
     // whenever toSearchFor changes, this function will run
     toSearchFor: function (newtoSearchFor, oldtoSearchFor) {
       this.debouncedGettoSearchFor()
     },
     clear: function (newClear, oldClear) {
       if (newClear) {
         this.toSearchFors = []
         this.selectedtoSearchFor = ''
         this.showPrompt = true
         this.showMatches = false
         this.allowNewButton(false)
         this.clear = false
         this.toSearchFor = ''
         this.$refs.toSearchFor.focus()
       }
     }
   },
   created: function () {
     // Limit how often we access the api, waiting until the user has completely
     // finished typing before making the ajax request.
     this.debouncedGettoSearchFor = _.debounce(this.gettoSearchFor, 500)
     this.config = _.capitalize(this.model)
     this.prompt = 'Enter ' + _.toLower(this.model) + ' name, or part thereof'
   },
   methods: {
     gettoSearchFor: function () {
       if (this.toSearchFor === '') {
         this.showPrompt = true
         this.showMatches = false
         this.allowNewButton(false)
       } else {
         this.showPrompt = false
         this.allowNewButton(true)
         var sc = this
     axios.defaults.url = this.ajaxurl
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
               if (typeof sc.toSearchFors.find(o => _.toLower(o.name) === _.toLower(sc.toSearchFor)) !== 'undefined') {
                 sc.allowNewButton(false)
               }
               sc.toSearchFors = response.data
               sc.showMatches = true
             }
           })
           .catch(function (error) {
             sc.prompt = 'Error! Unable to retrieve matches. '
             console.log(error)
           })
         }
       },
     displayProperty(theSelectedObject) {
       switch(this.config) {
         case 'Address': return theSelectedObject.address; break;
         default: return theSelectedObject.name;
       }
     },
     allowNewButton(showButton) {
       this.showNewButton = this.allowNew && showButton
     }
   }
});