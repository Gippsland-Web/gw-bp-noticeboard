<script>
(function ($) {
    

    $(document).ready(function () {
        $('form.new-notice-entry').live("submit",function (event) {
                event.preventDefault();
                $('.bp-user-notice-message').remove();
                $.post("/wp-admin/admin-ajax.php", $(this).serialize(), function (data) {

                    if(data.result == false){
                        var html = '<div id="message" class="bp-user-notice-message error"><p>';
                        $.each(data.errors, function () {
                            html += this+'<br>';
                        });
                        html += '</p></div>';
                        $(html).insertAfter($('form.new-notice-entry'));
                    } else {
                        var html = '<div id="message" class="bp-user-notice-message success"><p>Your notice has been posted</p></div>';
                        $(html).insertAfter($('form.new-notice-entry'));
                        $('form.new-notice-entry').slideUp();
                    }
                });
            }
        );
    });


$(document).ready(function () {
$('form.new-notice-btn').submit(function (event) {
event.preventDefault();
var html = '<div class="panel panel-success"><div class="panel-heading "><span class="notice-title">NEW NOTICE</span></div><div class="panel-body"><form class="form new-notice-entry">                            <label for="title"> </label><input name="title" class="form-control" type="text"  placeholder="Notice Title e.g. Help wanted in Orange, NSW"> </input><br ><label><span style="font-weight:400"><em>Select a region</em><span></label> <select name="region">        {% for r in regions %}            <option>{{r.name}}</option>        {% endfor %} </select> <br><label> </label><textarea class="form-control" name="content" placeholder="Type your Notice here"></textarea> <br>{{function("wp_nonce_field","new-notice")}} <input type="hidden" name="action" value="gw_new_notice"> <input type="submit" class="et_pb_button  et_pb_button_0 et_pb_module et_pb_bg_layout_light" value="Post"></button></form> </div></div>';

$('form.new-notice-btn').after(html);
$('form.new-notice-btn').slideUp();
 } );
 });


})(jQuery)
</script>

<div class="row text-center">
  
    <div class="col-md-4 col-sm-12">
       <div class="et_pb_button_module_wrapper et_pb_module et_pb_button_alignment_center">
				<a class="et_pb_button  et_pb_button_0 et_pb_module et_pb_bg_layout_light" > {{type}} Notices</a>
	</div>
  </div>
    {% if type == 'Host' %}
   
   <div class="col-md-4 col-sm-12">
	<div class="et_pb_button_module_wrapper et_pb_module et_pb_button_alignment_center"><a class="et_pb_button  et_pb_button_0 et_pb_module et_pb_bg_layout_light" href="/notice-board/?t=w">View WWOOFER Notices</a>
	</div>
  </div>

    {% else %}
     <div class="col-md-4 col-sm-12">
	<div class="et_pb_button_module_wrapper et_pb_module et_pb_button_alignment_center"><a class="et_pb_button  et_pb_button_0 et_pb_module et_pb_bg_layout_light" href="/notice-board/?t=h">View Host Notices</a> 
	</div>
   </div>

    {% endif %}
   <div class="col-md-4 col-sm-12">
        <form class="new-notice-btn">
          <div class="et_pb_button_module_wrapper et_pb_module et_pb_button_alignment_center"><input class="et_pb_button  et_pb_button_0 et_pb_module et_pb_bg_layout_light" type="submit" value="New Notice"></input></div>
        </form>
        
   </div>
   <div class="col-md-12">
       <form class="filter-notice-btn">  
       <select type="submit" name="r" onchange="this.form.submit()">
         
       <option>Filter by Region</option>
       {% for r in regions %}
           <option>{{r.name}}</option>
       {% endfor %}

       </select>
  {% if type == 'Host' %}
  <input type="hidden" name="t" value="h"></input>
   {% else %}
  <input type="hidden" name="t" value="w"></input>
   {% endif %}

       </form>
     
   </div>
<br>
        {% for n in data %}
<div class="col-md-6 col-sm-12">
        <div class="panel panel-success" style="margin-top:12px">
            <div class="panel-heading">
            <h3 class="notice-title">{{n.post_title}}</h3>
           </div>
                    <p style="margin-top:6px"><strong>Posted: </strong>{{n.post_date}} 
                    {% set reg = function('get_the_terms',n.ID,'notice_region')[0]  %} <br>
                    
                    {% if reg.name  is defined %}
                    <strong>Region:</strong> {{ reg.name }}</p>
                    {% endif %}
               
            <div class="panel-body" style="background-color:#ffffff">            
                    <div class="row">
                         <div class="col-md-3">
                        <a href="/members/{{function('bp_core_get_user_displayname',n.post_author)}}">
                            {{function('bp_core_fetch_avatar',{"item_id": n.post_author, "type": 'thumb'})}}
                            <p>{{function('bp_core_get_user_displayname',n.post_author)}}</p>
                        </a>
                      </div>
                       <div class="col-md-9">  
 <p>{{n.post_content|striptags}}</p>
                       </div>
                    </div>
            </div>
           <div class="panel-footer">
               <!--  Renders the button to PM user -->
               {{function('gw_nb_getMessageURL',n.post_author)}} 
              <!-- <a class="btn" href="/members/{{function('bp_core_get_user_displayname',n.post_author)}}">View Profile</a>-->
           </div>    
        </div>   
</div>
        {% endfor %}
    </div>
