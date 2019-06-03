// const initDateTimeInputs = () => {

//     const start_el = document.createElement('div');
//     start_el.innerHTML = '<div class="asd">asd</div>';

//     $('#start_date_btn').popover({
//         content: `<div style="position: relative"><div class="input-group date" id="datetimepicker_start" data-target-input="nearest">
//         <input id="start_input" type="text" class="form-control datetimepicker-input" data-target="#datetimepicker_start" value="" name="start_timestamp"/>
//         <div class="input-group-append" data-target="#datetimepicker_start" data-toggle="datetimepicker">
//             <div class="input-group-text"><i class="fa fa-calendar"></i></div>
//         </div>
//     </div></div>`,
//         html: true,
//         container: '#search-box > form',
//     })
//     $('#end_date_btn').popover({
//         content: `<div class="input-group date" id="datetimepicker_end" data-target-input="nearest">
//         <input id="end_input" type="text" class="form-control datetimepicker-input" data-target="#datetimepicker_end" value="" name="end_timestamp"/>
//         <div class="input-group-append" data-target="#datetimepicker_end" data-toggle="datetimepicker">
//         <div class="input-group-text"><i class="fa fa-calendar"></i></div>
//         </div>
//         </div>`,
//         html: true,
//         container: '#search-box > form',
//     })
// }

// initDateTimeInputs();


// //necessary for enter to work, ue to datetime inputs
// $('#search-box > form input[name="search"]').keypress(e => {
//     if (e.which == 13) {
//       $('#search-box > form').submit();
//       return false;
//     }
// });

// //maintain user input between opening and closing popover

// $('#start_date_btn').on('inserted.bs.popover', function () {
//     $('#datetimepicker_start').datetimepicker({
//         locale: 'pt',
        
//     });
//     console.log('====================================');
//     console.log("hello");
//     console.log('====================================');
//     $('#datetimepicker_start').on('hide.datetimepicker', function (e) {
//         console.log('====================================');
//         console.log("hide");
//         console.log('====================================');
        
//         $('#start_date_btn').popover('dispose');

//     })
    
// })
// $('#start_date_btn').on('hide.bs.popover', function () {
//     const val = $('#start_input').val()
//     $('#start_hidden_input').val(val);
// })
// $('#end_date_btn').on('inserted.bs.popover', function () {
//     $('#datetimepicker_end').on('change.datetimepicker', function (e) {
//         console.log('====================================');
//         console.log("hello");
//         console.log('====================================');
//         $('#end_date_btn').popover('dispose');
//     })
// })
// $('#end_date_btn').on('hide.bs.popover', function () {
//     const val = $('#end_input').val()
//     $('#end_hidden_input').val(val);
// })



