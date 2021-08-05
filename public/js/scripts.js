$(function () {
    $.fn.serializeObject = function()
    {
       var o = {};
       var a = this.serializeArray();
       $.each(a, function() {
           fix_name = this.name.replace(/[\[0-9]*[\]']+/g, '');
           if (o[fix_name]) {
               if (!o[fix_name].push) {
                   o[fix_name] = [o[fix_name]];
               }
               o[fix_name].push(this.value || '');
           } else {
               o[fix_name] = this.value || '';
           }
       });
       return o;
    };
    
    $.fn.filterByData = function(prop, val) {
        return this.filter(
            function() { return $(this).data(prop)==val; }
        );
    };
    
    if (!$.concat) {
        $.extend({
            concat: function(a, b) {
                var r = [];
                for (var x in arguments) {
                    if (typeof x == 'object') r = r.concat(arguments[x]);
                }
                return r;
            }
        });
    };

    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true
    });

    $('.selectpicker').selectpicker();
    $(".begin button:first").focus();
    
    $("#judgementJudgeCourt").on("change", function () {
        $.ajax({
            type: "GET",
            url: "../refresh_judgement_form",
            data: {judge_court_id: $(this).val(), "_token": $("[name=_token]").val() },
            cache: false,
            success: function (data) {
                $("#dates_container").html(data.dates_dp);
                $("#judges_container").html(data.judges_dp);
                $('.selectpicker').selectpicker();
                
                $('#articles, #judgment_type_id, #decision_source, #status_id, #speciality_id').removeClass('hidden');
                
                for( field in data.fields_to_hide )
                {
                    $('#' + data.fields_to_hide[field]).val(0);
                    $('#' + data.fields_to_hide[field]).addClass('hidden');
                }
            }
        });
    })
    
    $("#monthlyReportJudgeCourt").on("change", function () {
        $.ajax({
            type: "GET",
            url: "../refresh_fields",
            data: {judge_court_id: $(this).val(), monthly_report_id:$("[name=id]").val(),  '_token': $("[name=_token]").val() },
            cache: false,
            success: function (data) {
                
                $("#fieldsContainer").html(data.fields);
                $('#fields_container div').removeClass('hidden');
                $('#judgesDropdown').selectpicker('val', data.judge_id);
                
                for( field in data.fields_to_hide )
                {
                    $('#' + data.fields_to_hide[field]).addClass('hidden');
                }
            }
        });
    })
    
    $("#monthlyReportJudgeCourtBulk").on("change", function () {
        var url = $(this).data('url');
        $.ajax({
            type: "GET",
            url: url + "/refresh_fields_bulk",
            data: {judge_court_id: $(this).val(), monthly_report_id:$("[name=id]").val(),  '_token': $("[name=_token]").val() },
            cache: false,
            success: function (data) {
                var remove_flag = false;
                $(".fields_table th").each(function(){
                    if( $(this).hasClass('after_auto_titles') )
                        remove_flag = false;
                    
                    if( remove_flag == true )
                        $(this).remove();
                    
                    if( $(this).hasClass('before_auto_titles') )
                        remove_flag = true;
                });
                
                $("th.before_auto_titles").after(data.titles);
                
                var remove_flag = false;
                $(".fields_table td").each(function(){
                    if( $(this).hasClass('after_auto_fields') )
                        remove_flag = false;
                    
                    if( remove_flag == true )
                        $(this).remove();
                    
                    if( $(this).hasClass('before_auto_fields') )
                        remove_flag = true;
                });
                
                $("td.before_auto_fields").after(data.fields);
                
                $(".fields_table tbody tr").each(function(i,el){
                    $(this).find("input[name^=separated]").each(function(){
                        old_name = $(this).attr('name');
                        new_name = old_name.replace('[]','['+i+']');
                        $(this).attr('name',new_name);
                    })
                })
                
                $('table input').closest('td').removeClass('hidden');
                $('table th').removeClass('hidden');
                
                for( field in data.fields_to_hide )
                {
                    $('input[name^=' + data.fields_to_hide[field] + ']').val(0);
                    $('input[name^=' + data.fields_to_hide[field] + ']').closest('td').addClass('hidden');
                    $('th.' + data.fields_to_hide[field]).addClass('hidden');
                }
                $('#judgesDropdown').selectpicker('val', data.judge_id);
                
                $("input[name=court_name]").val(data.court_name);
                $("input[name=court_type]").val(data.court_type);
                $("input[name=specialities]").val(data.specialities);
            }
        });
    })
    
    $("select[name=id],select[name=type_id]").on("change", function()
    {
        var name_id = $("select[name=id]").val();
        if(name_id > 0)
        {
            protocol = $(this).data('uri');
            location.href = protocol + "/" + $("select[name=id]").val() + "/" + $("select[name=type_id]").val()
        }
    })
    
    $("#remove_photo").on("click", function(e){
        e.preventDefault();
        $("input[name=remove_photo]").val(1);
        $(this).parent('div').remove();
    })
});