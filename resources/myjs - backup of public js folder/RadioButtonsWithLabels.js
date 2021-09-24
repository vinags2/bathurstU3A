/**
 * RadioButtonsWithLabels.js
 * 
 * Requires the following in the blade file, before loading this file.
 * <script src = "{{ asset('js/RadioButtonWithLabel.js') }}"></script>
 * 
 * - vue component that outputs a label and a checkbox
 * Properties that must be passed from the calling html
 *    name is the name passed to the request, and is required
 *    labelsAndValues (labels-and-values in the HTML) are the label and values in array of object format
 *      - get them from the page's Composer which should get them from Setting::effectiveFromOptions()
 *    checkedValue (check-value in the HTML) is the value of the checkbox that should be checked.
 */

Vue.component('RadioButtonsWithLabels',{
   props: {
     name: {
       type: String,
       required: true
     },
     labelsAndValues: {
        type: Array,
        required: true
     },
     checkedValue: {
       type: String,
       required: true
     }
   },
   Components: {
      RadioButtonWithLabel: "RadioButtonWithLabel"
   },
   template: `<div>
      <RadioButtonWithLabel v-for="labelAndValue in labelsAndValues" :key="labelAndValue.value"
          name="name"
          :value="labelAndValue.value"
          :label="labelAndValue.label"
          :isChecked="labelAndValue.value == checkedValue">
      </RadioButtonWithLabel>
   </div>`
});