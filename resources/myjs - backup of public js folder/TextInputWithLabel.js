/**
 * TextInputWithLabel.js
 * 
 * - vue component that outputs a label and an input of type text
 * Properties that must be passed from the calling html
 *    name is the name passed to the request, and is required
 *    label is the label that replaces the label text. The default is 'Name'
 *    value is the default value of the input text control, and defaults to the empty string
 *    isError - if true the input box is highlighted, and defaults to false
 *    setFocus - if exists, set the focus to this control. Default = false.
 */
Vue.component('TextInputWithLabel',{
   props: {
     name: {
       type: String,
       required: true
     },
     label: {
        type: String,
        default: 'Name'
     },
     title: {
        type: String,
     },
     buttonTitle: {
        type: String,
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
            {{label}}:
          </label>
        </div>
        <div class="col-4">
          <input
            type      ="text"
            ref       ="textInput"
            class     ="w-100"
            :autofocus="setFocus"
            :class    ="{'is-invalid': isError }"
            :name     ="name"
            :value    ="inputValue"
            :title    = "title"
          />
        </div>
        <div class="col-1">
          <button
            type      ="button"
            class     ="btn btn-outline-secondary btn-sm disabled ml-n4"
            @click    ="clear"
            title    ="Click to clear."
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