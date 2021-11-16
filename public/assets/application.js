$(document).ready(function() {
    console.log("Ready")

    $("#app-form").submit(function(event) {
        event.preventDefault()
        let response = create_response()
        $.post("/application.php", response,
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
        response.m_type = $("#m_type").val()
        response.firstName = $("#firstName").val()
        response.lastName = $("#lastName").val()
        response.email = $("#email").val()
        response.phone = $("#phone").val()
        response.studentId = $("#studentId").val()
        response.questions = []

        let questions = $(".question")
        for (let i = 0; i < questions.length; i++) {
            let question = {}
            question.questionId = $(questions[i]).attr("id")
            question.optionId = $(questions[i]).val()
            response.questions.push(question)
        }
        console.log(response)
    }
})