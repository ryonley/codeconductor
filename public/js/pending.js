/**
 * Created with JetBrains PhpStorm.
 * User: Rodger
 * Date: 11/10/13
 * Time: 10:25 PM
 * To change this template use File | Settings | File Templates.
 */



function waitForChallenger(game_id){
    var myInterval = setInterval( function(){

        $.ajax({
            url: '/games/ready',
            type: 'POST',
            dataType: 'json',
            // WE NEED TO SEND THE TIMESTAMP OF THE MARK WE JUST CREATED
            data: {'game_id' : game_id},
            success: function(response){
                console.log(response);
                if(response.ready == true){
                    console.log('its ready');
                    // update the class of the "Awaiting Challenger Text"
                    // replace it with a link to the page
                    $('#game_wait').hide();
                    $('#game_key').show();
                }

            }
        });
        // REPEATS EVERY 5 SECONDS UNTIL clearInterval IS CALLED
    },5000);
}

