$(document).ready(function() {
    console.log("Ready")

    $("#app-form").submit(function(event) {
        event.preventDefault()
        let formData = create_response()
        $.post("/application.php", formData,
            function(response) {
                // show success message
                console.log(response)
            })
            .fail(function(response) {
                // show popup with error message
                console.log(response.responseText)
                window.alert(JSON.parse(response.responseText).message)
            })
    })

    // helper functions
    const create_response = function () {
        let response = {}
        // TODO add in match form id
        response.matchFormId = $("#match-form-id").val()
        response.mType = $("#m-type").val()
        response.firstName = $("#first-name").val()
        response.lastName = $("#last-name").val()
        response.email = $("#email").val()
        response.phone = $("#phone").val()
        response.studentId = $("#student-id").val()
        response.questions = []

        let questions = $(".question")
        for (let i = 0; i < questions.length; i++) {
            let question = {}
            question.questionId = $(questions[i]).attr("id")
            question.optionId = $(questions[i]).val()
            response.questions.push(question)
        }

        return response
    }
})