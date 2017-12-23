(function ($, window, Drupal, drupalSettings) {
    Drupal.behaviors.takeQuizCountdown = {
        attach: function (context, settings) {
            //Get question's time data.
            // When second greater than 0,the exam is count down mode.
            // When second equal to 0,the exam is time counting mode.
            var second = parseInt($("#takeQuizCountdownDiv :hidden").val());
            //The flag to know time is couning down or increasing.
            var isCountDown = true;
            if (second == 0) {
                //Time counting mode.There is no time limitation.
                isCountDown = false;
                //In this mode timer is increasing per second.
                timer = setInterval(function () {
                    second += 1;
                }, 1000);
            } else {
                //Counting down mode.Time is limited.
                var timer = null;
                //In this mode timer is reducing per second.
                timer = setInterval(function () {
                    second -= 1;
                    if (second <= 0) {
                        $("#takeQuizSubmitButtonDiv :submit").trigger("click");
                    }
                }, 1000);
            }
            $("#takeQuizSubmitButtonDiv :submit").on("click", function () {
                clearInterval(timer);
                $("#takeQuizEndTimeDiv :hidden").val(Math.floor($.now() / 1000));
                if (isCountDown) {
                    $("#takeQuizStartTimeDiv :hidden").val(Math.floor($.now() / 1000 - (parseInt($("#takeQuizCountdownDiv :hidden").val()) - second)));
                } else {
                    $("#takeQuizStartTimeDiv :hidden").val(Math.floor($.now() / 1000 - second));
                }
                return true;
            });
        }
    }

})(jQuery, this, Drupal, drupalSettings);