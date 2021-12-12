$(document).ready(function() {
    console.log("Ready")

    // services
    $("#make-matches").click(function(event) {
        let id = $("#match-form-id").val()
        let formData = {
            "action" : "make_matches"
        }
        $.post(`/?page=view_applicants&id=${id}`, formData,
            function(response) {
                // show success message
                $('#match-data').empty()
                let receivedData = ''
                let responseJSON = $.parseJSON(response)
                $(responseJSON).each(function(match) {
                    let row = `
                        <tr>
                            <td>${responseJSON[match]['match_id']}</td>
                            <td>${responseJSON[match]['date_created']}</td>
                            <td>${Math.round(responseJSON[match]['confidence_rate']) * 100}%</td>
                            <td>${responseJSON[match]['mentee_application_id']}</td>
                            <td>${responseJSON[match]['mentee_first_name']}</td>
                            <td>${responseJSON[match]['mentee_last_name']}</td>
                            <td>${responseJSON[match]['mentee_email']}</td>
                            <td>${responseJSON[match]['mentor_application_id']}</td>
                            <td>${responseJSON[match]['mentor_first_name']}</td>
                            <td>${responseJSON[match]['mentor_last_name']}</td>
                            <td>${responseJSON[match]['mentor_email']}</td>
                        </tr>
                    `
                    receivedData += row
                })
                console.log(receivedData)
                $('#match-data').html(receivedData)
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