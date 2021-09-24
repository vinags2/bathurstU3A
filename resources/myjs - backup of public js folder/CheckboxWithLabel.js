/**
 * CheckboxWithLabel.js
 * 
 * - vue component that outputs a label and a checkbox
 * Properties that must be passed from the calling html
 *    name is the name passed to the request, and is required
 *    label is the label that replaces the label text. The default is 'Suspended'
 *    isChecked (is-checked in the HTML) is true if the checkbox should be checked. The default is false.
 *    leftPlacement is true if the checkbox is on the left of the label (checkbox is too small when left-placed)
 */
Vue.component('CheckboxWithLabel',{
   props: {
     name: {
       type: String,
       required: true
     },
     label: {
        type: String,
        default: 'Suspended'
     },
     isChecked: {
       type: Boolean,
       default: false
     },
     leftPlacement: {
       type: Boolean,
       default: false
     }
   },
   template: `
      <div class="row">
        <div v-if="rightPlacement" :class="classForLabel">
          <label>
            {{label}}:
          </label>
        </div>
        <div :class="classForCheckbox">
          <input
            type      ="checkbox"
            :name     ="name"
            :checked  ="inputValue"
            :class="classForCheckbox"
          />
        </div>
        <div v-if="leftPlacement" :class="classForLabel">
          <label>
            {{label}}:
          </label>
        </div>
      </div>`,
   data: function() {
     return {
       inputValue: false,
       rightPlacement: !this.leftPlacement,
       classForCheckbox: this.leftPlacement ? "col-1" : "col-4",
       classForLabel: this.leftPlacement ? "col-4" : "col-1"
     }
   },
   created: function() {
      this.inputValue = this.isChecked
   }
});