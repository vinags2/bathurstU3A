/**
 * RadioButtonWithLabel.js
 * 
 * - vue component that outputs a label and a radio button
 * Properties that must be passed from the calling html
 *    name is the name passed to the request, and is required
 *    value is the value of the control
 *    label is the label that replaces the label text. Required.
 *    isChecked (is-checked in the HTML) is true if the radio button should be checked. The default is false.
 */
Vue.component('RadioButtonWithLabel',{
   props: {
     name: {
       type: String,
       required: true
     },
     label: {
        type: String,
        required: true
     },
     isChecked: {
       type: Boolean,
       default: false
     },
     value: {
       required: true
     }
   },
   template: `
      <div class="row mb-n2">
          <input
            type      ="radio"
            :name     ="name"
            :checked  ="inputValue"
            :value    ="value"
          />
        <div class="col-4">
          <label>
            {{label}}
          </label>
        </div>
      </div>`,
   data: function() {
     return {
       inputValue: false,
     }
   },
   created: function() {
      this.inputValue = this.isChecked
   }
});