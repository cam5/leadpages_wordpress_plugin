(function ($) {

    $(function () {


        var $body = $('body');

        function init() {
            timedLeadBoxes();
            exitLeadBoxes();
            setPostTypes();
            $('#leadboxesLoading').hide();
            $('#timedLoading').hide();
            $('#exitLoading').hide();
            $('.ui-loading').hide();
            $("#leadboxesForm").show();
        }

        init();


        $body.on('change', '#leadboxesTime', function () {
            if($(this).val() == 'none'){
                $('#selectedLeadboxSettings').hide();
            }
            populateTimedStats(this);

        });

        if($("#leadboxesTime").val() != 'none'){
            populateTimedStats($("#leadboxesTime"));
        }
        if($("#leadboxesExit").val() != 'none'){
            populateExitStats($("#leadboxesExit"));
        }

        $body.on('change', '#leadboxesExit', function () {
            if($(this).val() == 'none'){
                $('#selectedExitLeadboxSettings').hide();
            }
            populateExitStats(this);

        });

        $body.on('click', '#timedLeadboxRefresh', function(){
            $('#timedLoading').css('display', 'inline');
            $.ajax({
                type : "GET",
                url : leadboxes_object.ajax_url,
                data : {
                    action: "allLeadboxesAjax"
                },
                success: function(response) {
                    $('ui-loading').hide();
                    var leadboxes = $.parseJSON(response);
                    $('.timeLeadBoxes').html(leadboxes.timedLeadboxes);
                }
            });

        });

        $body.on('click', '#exitLeadboxRefresh', function(){
            $('#exitLoading').css('display', 'inline');
            $.ajax({
                type : "GET",
                url : leadboxes_object.ajax_url,
                data : {
                    action: "allLeadboxesAjax"
                },
                success: function(response) {
                    $('#exitLoading').hide();
                    var leadboxes = $.parseJSON(response);
                    $('.exitLeadBoxes').html(leadboxes.exitLeadboxes);
                }
            });

        });

        function populateTimedStats($this) {
            var timeTillAppear = $($this).find(':selected').data('timeappear');
            var pageView = $($this).find(':selected').data('pageview');
            var daysTillAppear = $($this).find(':selected').data('daysappear');

            var stats = '<ul class="leadbox-stats">'+
                stat_row("Time before it appears: ", timeTillAppear + ' seconds') +
                stat_row("Page views before it appears: ", pageView + ' views') +
                stat_row("Don't reshow for the next: ", daysTillAppear + ' days') +
                    '</ul>';
            $("#selectedLeadboxSettings").html(stats);
        }

        function populateExitStats($this) {
            var daysTillAppear = $($this).find(':selected').data('daysappear');
            var stats ='<ul class="leadbox-stats">'+
                stat_row("Don't reshow for the next ", daysTillAppear + ' days')+
                '</ul>';
            $("#selectedExitLeadboxSettings").html(stats);
        }

        function stat_row(label, value) {
            return '<li>'+ label + value+'</li>';

        }

        function timedLeadBoxes() {
            $('.timeLeadBoxes').html(leadboxes_object.timedLeadboxes);
        }

        function exitLeadBoxes() {
            $('.exitLeadBoxes').html(leadboxes_object.exitLeadboxes);
        }

        function setPostTypes() {
            $('.postTypesForTimedLeadbox').html(leadboxes_object.postTypesForTimedLeadboxes);
            $('.postTypesForExitLeadbox').html(leadboxes_object.postTypesForExitLeadboxes);
            $('.postTypesForExitLeadbox').html(leadboxes_object.postTypesForExitLeadboxes);
        }

    });

}(jQuery));