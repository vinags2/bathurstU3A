
        <table>
            <form action="" autocomplete="off" method="GET">
            <tr><td></td></tr>
            <tr>
                <td><label class="col-xs-3 col-form-label mr-2" for="first_name_input">First Name:</label></td>
                <td>
                    <input type="text" autofocus="autofocus" size="40" id="first_name_input" class="form-control" name="first_name" value="{{ $person->first_name }}" />
                </td>
            </tr>
            <tr>
                <td><label class="col-xs-3 col-form-label mr-2" for="last_name_input">Last Name:</label></td>
                <td>
                    <input type="text" id="last_name_input" data-url="{{ url('namesearch') }}" class="form-control" name="last_name" value="{{ $person->last_name }}" />
                    <input type="hidden" id="name_url" value="{{ url('closenamesearch') }}" name="name_url" />
                </td>
            </tr>
            <tr>
                <td></td>
                <td> 
                        <select id="name_matches"  size="10" style="display:none" name="memberId" class="form-control custom-select" onchange="this.form.submit()">
                        </select>
                </td>
            </tr>
            <tr>
                <td></td>
                    <td>
                        <input  id="clear_names" type="checkbox" name="clearNames" onChange="ajaxClearValues(ajaxMember)" class="form-check-input ml-3">
                        <label class="form-check-label ml-5" for="clearNames">clear</label>
                </td>
            </tr>
            <tr>
                <td></td>
                        <td>
                           <button type="submit" id="newMember" name="newMember" value="1" style="display:none" class="btn btn-primary btn-sm">New Member</button>
                        </td>
            </tr>
            <tr><td>&nbsp;</td></tr>
            <input type="hidden" id="state" value="{{ $state }}" name="state" />
           </form>
        </table>