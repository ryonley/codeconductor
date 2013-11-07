/**
 * Created with JetBrains PhpStorm.
 * User: Rodger
 * Date: 8/18/13
 * Time: 10:57 PM
 * To change this template use File | Settings | File Templates.
 */
jQuery(function($) {

    if($('#listen').length > 0){
       // listen(time, game_id);
        console.log('listening');
        var time = $('#timestamp').val();
        var game_id = $('#game_id').val();
        listen(time, game_id);
    }

    $('#gameboard').on('click', '.enabled', function(e){
        e.preventDefault();
        // GET THIS POSITION ID
        var td_element = $(this);
        var position_id = td_element.attr('id');
        /**
         * NEED TO PASS THE GAME ID AND THE PLAYER ID ALSO
         */
        console.log(td_element);

        $.ajax({
           url: '/tic-tac-toe/move',
            type: 'POST',
            dataType: 'json',
            data: {position: position_id},
            success: function(response){
                console.log(response);
                if(response.success == true){
                    // the chosen square is marked with this players mark
                    var mark = response.mark;
                    var time = response.timestamp;
                    var game_id = response.game_id;
                    var winner = response.winner;
                    var winning_positions = response.winning_positions;
                    var game_over = response.game_over;
                    var custom_text = response.custom_text;

                    td_element.text(mark);
                    $('#gameboard td').each(function(){
                        $(this).removeClass('enabled').addClass('disabled');
                    });

                    // DO THE FOLLOWING IF WINNER IS FALSE
                    if(winner === false){
                        if(game_over === true){
                            $('#turn').text('Game over... No place else to go.');
                        } else {
                            $('#turn').text('Its not your turn');
                            // the listener is engaged
                            listen(time, game_id);
                        }

                    } else {
                        $('#turn').text(custom_text);

                    }


                }
            }
        });
    });


    function listen(time, game_id){
        var myInterval = setInterval( function(){

            $.ajax({
                url: '/tic-tac-toe/update',
                type: 'POST',
                dataType: 'json',
                // WE NEED TO SEND THE TIMESTAMP OF THE MARK WE JUST CREATED
                data: {'timestamp' : time, 'game_id' : game_id},
                success: function(response){

                    if(response.success == true){
                        // WE NEED TO KNOW WHAT THE NEW POSITION IS THAT WAS CHOSEN BY THE OPPONENET
                        // AND WHAT THAT PLAYER'S MARK IS

                        console.log(response);
                        var mark = response.mark;
                        var position_id = response.position_id;

                        $('#'+position_id).text(mark);
                        $('#turn').text('Its your turn');

                        $('#gameboard td').each(function(){
                            $(this).removeClass('disabled').addClass('enabled');
                        });

                        // ONCE THE NEW DATA HAS BEEN OBTAINED, END setInterval
                        clearInterval(myInterval);
                    } else {
                        console.log('nothing yet');
                    }

                }
            });
            // REPEATS EVERY 5 SECONDS UNTIL clearInterval IS CALLED
        },5000);
    }


});