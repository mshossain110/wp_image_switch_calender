
(function($) {

	function getMedia (date) {
        $('#image-pregiew').find('a').remove();
        $('#tc-loading').show();
        
		$.ajax({
			url: WP_ISC.ajaxurl,
			type: 'get',
			data: {
				action: 'get_image',
				date: date
			},
			success: function( result ) {
				appendImge(result)
			}
		})
	}

    function appendImge (data) {
        $('#tc-loading').hide();
        if (data.data.length) {
            $('#image-pregiew').append(`<a data-fancybox="gallery" href="${ data.data[0].guid}"><img src="${ data.data[0].guid}" /></a>`);
        }
        
        // $('[data-fancybox="gallery"]').fancybox({})
        
    }
	
	$( "#tc-datepicker" ).datepicker({
        dateFormat: "dd-mm-yy",
		onSelect: function(date, obj){
			getMedia(date)
		}
	});
    

    $('#tc-yesterday').click(function(){
        var date = new Date;
        var day = date.getDate();

        var month = date.getMonth();

        day = day -1;
        if (day < 10) {
            day = '0' + date;
        }
        month = month + 1;
        month = month < 10 ? '0' + month : month;
        getMedia(`${ day }-${month}-${ date.getFullYear() }`);
    })

    $('#tc-today').click(function(){
        var date = new Date;
        var day = date.getDate();
        var month = date.getMonth();

        if (day < 10) {
            day = '0' + date;
        }
        month = month + 1;
        month = month < 10 ? '0' + month : month;
        getMedia(`${ day }-${month}-${ date.getFullYear() }`);
    })

    $('#tc-tomorrow').click(function(){
        var date = new Date;
        var day = date.getDate();

        var month = date.getMonth();

        day = day + 1;
        if (day < 10) {
            day = '0' + date;
        }
        month = month + 1;
        month = month < 10 ? '0' + month : month;
        getMedia(`${ day }-${month}-${ date.getFullYear() }`);
    })

    function todayRequies () {
        var date = new Date;
        var day = date.getDate();
        var month = date.getMonth();

        if (day < 10) {
            day = '0' + day;
        }
        month = month + 1;
        month = month < 10 ? '0' + month : month;

        getMedia(`${ day }-${month}-${ date.getFullYear() }`);
    }



    todayRequies();
	date_time('tc-stime');
	
})( jQuery );




function date_time(id)
{
        var date = new Date;
        var year = date.getFullYear(),
         month = date.getMonth(),
        months = new Array('January', 'February', 'March', 'April', 'May', 'June', 'Jully', 'August', 'September', 'October', 'November', 'December'),
        d = date.getDate(),
        day = date.getDay(),
        days = new Array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'),
        h = date.getHours(),
        dd = (h >= 12) ? 'pm' : 'am';

        if(h<10) {
                h = "0"+h;
        }
        var m = date.getMinutes();
        if(m<10) {
            m = "0"+m;
        }
        var s = date.getSeconds();
        if(s<10)
        {
                s = "0"+s;
        }
        var result = ''+days[day]+' '+months[month]+' '+d+' '+year+' '+h+':'+m+':'+s + ' ' + dd;
        document.getElementById(id).innerHTML = result;
        setTimeout('date_time("'+id+'");','1000');
        return true;
}