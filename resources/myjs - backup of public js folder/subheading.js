// subheadingnoform.js
// - vue component that formats a subheading
// - 'noform' means that this component must wrapped in a form to be submitted. There is no 'form' control in this component
// - model must exist in the database
// Properties that must be passed from the calling html
// subheading: eg 'Course details' which will be formatted as a subheading.
// width: the width of the div (eg 6 will generate a class="col-6")
Vue.component('subheading',{
   props: {
     subheading: {
       type: String,
       required: true
     },
     width: {
       type: Number,
       default: 2
     }
   },
   template: `
      <div class="row">
          <span v-bind:class="classwidth">
              <b>{{subheading}}</b>
          </span>
      </div>`,
    data: function() {
      return {
        classwidth: " border border-primary mb-2 col-"+this.width
      }
    }
});