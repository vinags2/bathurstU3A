<!--
    Paramaters required by this partial blade:
    - $model (eg, Course, Venue, Member)
    - $modelName (eg $courses->name, $venue->name, $person->full_name)
    - $url -- the url to pass to the API
    - $paramKey -- the name of the parameter to pass in the url to the API [optional; default = 'name']
    - $allowNewModel -- boolean as to whether the user to pick a new model which doesn't exist in the database [optional; default = false]
    - $identifier - must be unique for other calls to this blade partial. Can be anything which is a valid id and variable name.
-->
    <form id="{{$identifier}}" v-bind:action="href" autocomplete="off" method="GET">
        <table>
            <tr><td></td></tr>
            <tr>
                <td><label class="col-xs-3 col-form-label mr-2" for="first_name_input">@{{config}}:</label></td>
                <td>
                    <input type="text" ref="toSearchFor" autofocus="autofocus" v-model="toSearchFor" size="40" id="toSearchFor_input" class="form-control" name="toSearchFor" value="{{ $modelName }}" />
                </td>
            </tr>

            <tr>
                <td></td>
                <td> 
                    <select id="name_matches" v-model="selectedtoSearchFor" v-if="showMatches" size="6" name="findMatches" class="form-control custom-select" onchange="this.form.submit()">
                        <option disabled value="">If the name is in the list, please select it...</option>
                        <option v-for="toSearchFor in toSearchFors" v-bind:value="toSearchFor.id">@{{ toSearchFor.name }}</option>
                    </select>
                </td>
            </tr>
            <tr><td></td>
                <td v-if="showPrompt">
                    @{{ prompt }}

                </td>
                <td v-else="showPrompt">
                    <input id="clear_names" type="checkbox" v-model="clear" name="clearNames" class="form-check-input ml-3">
                    <label class="form-check-label ml-5" for="clearNames">clear</label>
                </td>
            </tr>
            <tr>
                <td></td>
                    <td v-if="showNewButton">
                        <button type="submit" id="newModel" name="newModel" value="1" class="btn btn-primary btn-sm">New @{{config}}</button>
                        <input type="hidden" id="state" value="{{ $state }}" name="state" />
                    </td>
            </tr>
            <tr><td>&nbsp;</td></tr>
        </table>
    </form>

        <script>
            var {{$identifier}} = new Vue({
            el: '#{{$identifier}}',
            data: {
                config: '{{ $model }}',
                axiosUrl: '{{ $searchUrl }}',
                axiosParamKey: '{{ $paramKey ?? name }}',
                allowNew: '{{ $allowNewModel ?? false }}', // allow New Courses

                toSearchFor: '',
                prompt: '',
                toSearchFors: [],
                selectedtoSearchFor: '',
                showPrompt: true,
                showMatches: false,
                showNewButton: false,
                clear: false,
                href: window.location.href
            },
            computed: {
                axiosParams() {
                    const params = new URLSearchParams();
                    params.append(this.axiosParamKey, this.toSearchFor);
                    return params;
                }
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
                axios.defaults.url = this.axiosUrl
                this.config = _.capitalize('{{ $model }}')
                this.prompt = 'Enter a ' + _.toLower(this.config) + ' name, or part thereof'
            },
            methods: {
                gettoSearchFor: function () {
                    if (this.toSearchFor === '') {
                        this.showPrompt = true
                        this.showMatches = false
                        // this.showNewButton = false
                        this.allowNewButton(false)
                    } else {
                        this.showPrompt = false
                        // this.showNewButton = true
                        this.allowNewButton(true)
                        var sc = this
                        axios({
                            method: 'get',
                            // url: '{{ url('coursesearch') }}',
                            responseType: 'json',
                            params: sc.axiosParams,
                            timeout: 2000
                        })
                            .then(function (response) {
                                if (response.data.length === 0) {
                                    sc.showMatches = false
                                } else {
                                    if (typeof sc.toSearchFors.find(o => _.toLower(o.name) === _.toLower(sc.toSearchFor)) !== 'undefined') {
                                        // sc.showNewButton = false
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
                allowNewButton(showButton) {
                    this.showNewButton = this.allowNew && showButton
                }
            }
            })
        </script>