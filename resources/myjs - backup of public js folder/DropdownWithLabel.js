/**
 * DropdownWithLabel.js
 * 
 * - vue component that outputs a label and a dropdown control
 * Properties that must be passed from the calling html
 *    name is the name passed to the request, and is required
 *    label is the label that replaces the label text. Required.
 *    selectedKey is the key of the option to be selected
 *    options are the options to display. Default are the days of the week.
 *    value is the value of the control
 *    isChecked (is-checked in the HTML) is true if the radio button should be checked. The default is false.
 */
Vue.component('DropdownWithLabel',{
   props: {
     name: {
       type: String,
       required: true
     },
     label: {
        type: String,
        required: true
     },
     title: {
       type: String,
     },
     options: {
       type: Array,
       default: function () {
         return [{key: 0, text: "Sunday"},
                {key: 1, text: "Monday"},
                {key: 2, text: "Tuesday"},
                {key: 3, text: "Wednesday"},
                {key: 4, text: "Thursday"},
                {key: 5, text: "Friday"},
                {key: 6, text: "Saturday"}]
       }
     },
     selectedKey: {
       default: 0
     }
   },
   template: `
      <div class="row">
        <div class="col-1">
          <label>
            {{label}}
          </label>
        </div>
        <div class="col-4">
          <select v-model="selected" :title="title">
            <option v-for="option in options" :value="option.key">
                {{ option.text }}
            </option>
          </select>
        </div>
      </div>`,
   data: function() {
     return {
       inputValue: false,
       selected: 1,
     }
   },
   created: function() {
      this.inputValue = this.isChecked
      this.selected = this.selectedKey
   }
});