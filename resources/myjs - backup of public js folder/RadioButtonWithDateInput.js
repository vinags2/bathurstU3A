/**
 * RadioButtonWithDateInput.js
 * 
 * - vue component that outputs a label and a radio button and an input of type date
 * Properties that must be passed from the calling html
 *    name is the name passed to the request, and is required
 *      note that the name of the input of type date is name + '_date'
 *    value is the value of the radio button. Required.
 *    dateValue (date-value in the HTML) is the value of the input date control. Required.
 *    label is the label that of the radio button. Required.
 *    isChecked (is-checked in the HTML) is true if the radio button should be checked. The default is false.
 */
Vue.component('RadioButtonWithDateInput',{
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
     },
     dateValue: {
       required: true
     }
   },
   template: `
      <div class="row">
        <div>
          <input
            type      ="radio"
            :name     ="name"
            :checked  ="inputValue"
            :value    ="value"
            class=""
          />
          <label class="mr-3">
            {{label}}
          </label>
          <input
            type="date"
            size="20"
            name="dateName"
            :value="dateValue">
        </div>
      </div>`,
   data: function() {
     return {
       inputValue: false,
       dateName: this.name + "_date"
     }
   },
   created: function() {
      this.inputValue = this.isChecked
   }
});