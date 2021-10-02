/**
 * TextAreaWithLabel.js
 * 
 * - vue component that outputs a label and a textarea
 * Properties that must be passed from the calling html
 *    name is the name passed to the request, and is required
 *    label is the label that replaces the label text. The default is 'Description'
 *    value is the default value of the textarea control, and defaults to the empty string
 *    isError - if true the textarea is highlighted, and defaults to false
 *    setFocus - if exists, set the focus to this control. Default = false.
 *    rows - number of rows to make the textarea, default = 7
 */
Vue.component('TextAreaWithLabel',{
   props: {
     name: {
       type: String,
       required: true
     },
     title: {
       type: String
     },
     rows: {
       type: Number,
       default: 7
     },
     label: {
        type: String,
        default: 'Description'
     },
     value: {
       type: String,
       default: ''
     },
     setFocus: {
       type: Boolean,
       default: false
     },
     isError: {
       type: Boolean,
       default: false
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
          <textarea
            ref       ="textInput"
            class     ="w-100"
            :autofocus="setFocus"
            :class    ="{'is-invalid': isError }"
            :name     ="name"
            :rows     ="rows"
            :title    ="title"
            :value    ="inputValue" >
            {{value}}
          </textarea>
        </div>
        <div class="col-1">
          <button
            type      ="button"
            class     ="btn btn-outline-secondary btn-sm disabled ml-n4"
            title     ="Click to clear."
            @click    ="clear"
          >
            X
          </button>
        </div>
      </div>`,
   data: function() {
     return {
       inputValue: ''
     }
   },
   created: function() {
      this.inputValue = this.value
   },
   methods: {
     clear: function (event) {
        this.inputValue = ''
        this.$refs.textInput.focus()
     }
    }
});