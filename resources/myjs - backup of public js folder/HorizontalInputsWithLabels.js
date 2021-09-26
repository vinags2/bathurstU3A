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

Vue.component('HorizontalInputsWithLabels',{
   props: {
     name1: {
       type: String,
       required: true
     },
     name2: {
       type: String,
       required: true
     },
     value1: {
       type: String,
     },
     value2: {
       type: String,
     },
     title1: {
       type: String
     },
     title2: {
       type: String
     },
     label: {
        type: String,
     },
     label1: {
        type: String,
        required: true
     },
     label2: {
        type: String,
        required: true
     },
     inputType: {
       type: String,
       default: 'text'
     }
   },
   template: `<div class="row">
      <div class="col-1" :title="firstTitle">
          <label>
            {{firstLabel}}
          </label>
      </div>
      <div class="col-4">
          <label v-if="label" :title="title1">
            {{label1}}
          </label>
          <input
            :type     ="inputType"
            :name     ="name1"
            :value    ="value1"
            :class    ="secondClass"
            size      ="3"
            :title    ="title1"   
            :checked  ="inputValue1"
          />
          <label class="ml-2" :title="title2">
            {{label2}}
          </label>
          <input
            :type     ="inputType"
            :name     ="name2"
            :value    ="value2"
            class     ="ml-2"
            size      ="3"
            :title    ="title2"   
            :checked  ="inputValue2"
          />
      </div>
   </div>`,
   data: function() {
     return {
       firstLabel: this.label ?? this.label1,
       secondLabel: this.label ? this.label1 : '',
       thirdLabel: this.label2,
       secondClass: this.label ? "ml-2" : "1",
       inputValue1: this.value1 == 'true',
       inputValue2: this.value2 == 'true',
       firstTitle: this.label ? "" : this.title1
     }
   }
});