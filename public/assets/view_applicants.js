$(document).ready(function() {

    // load general project data
    let id = $("#match-form-id").val()

    $.get(`/api/projects?id=${id}`, function(response) {
        // TODO: hide loading icons (still need to insert them)
        let projectData = JSON.parse(response)
        $("#project-title").text(projectData[0]['title'])
        $("#project-date-created").text(projectData[0]['date_created'])
    })
        .fail(function(error) {
            console.log(error)
        })

    // load applicants and matches, set interval to refresh every 10 seconds
    loadApplicants()
    loadMatches()

    setInterval(function() {
        loadApplicants()
        loadMatches()
    }, 10000)


    // make matches button
    $("#make-matches").click(function(event) {
        $.post(`/api/matches?id=${id}`, function(response) {
            loadMatches()
        })
        .fail(function(response) {
            // show popup with error message
            window.alert(JSON.parse(response.responseText).message)
        })
    })

    // delete project button
    $("#delete-match-form").click(function(event) {
        confirmDelete = confirm("Are you sure you wish to delete this match form? All data will be lost.")
        if(confirmDelete == true) {
            $.ajax({
                url: `/api/projects?id=${id}` ,
                type: 'DELETE',
                success: function(result) {
                    window.location.href = "/"
                }
            })
            .fail(function(response) {
                // show popup with error message
                console.log(response.responseText)
                window.alert(JSON.parse(response.responseText).message)
            })
        }
    })


    function loadApplicants() {
        $.get(`/api/applicants?id=${id}`, function(response) {
            // TODO: hide loading icons (still need to insert them)

            let mentorContainer = $("#mentor-container")
            $(mentorContainer).empty()

            let menteeContainer = $("#mentee-container")
            $(menteeContainer).empty()

            let applicants = JSON.parse(response)

            let mentor_count = 0
            let mentee_count = 0

            $(applicants).each(function(applicant) {
                let applicantRow = `
                <tr class="applicant-id" id=${applicants[applicant]['application_id']}>
                    <td>${applicants[applicant]['application_id']}</td>
                    <td>${applicants[applicant]['date_created']}</td>
                    <td>${applicants[applicant]['first_name']}</td>
                    <td>${applicants[applicant]['last_name']}</td>
                    <td>${applicants[applicant]['email']}</td>
                    <td>${applicants[applicant]['phone']}</td>
                    <td>${applicants[applicant]['stud_id']}</td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-secondary applicant-response-button" data-bs-toggle="modal" data-bs-target="#applicantResponseModal">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle" viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                    <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                                </svg>
                            </button>
                            <button type="button" class="btn btn-outline-secondary applicant-delete-button">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            `

                if(applicants[applicant]['m_type'] == "mentor") {
                    mentorContainer.append(applicantRow)
                    mentor_count++
                } else if (applicants[applicant]['m_type'] == "mentee") {
                    menteeContainer.append(applicantRow)
                    mentee_count++
                }
            })

            // reload buttons in DOM
            loadApplicantResponseButtons()
            loadApplicantDeleteButtons()

            // adjust stats
            $('#project-mentor-count').text(mentor_count)
            $('#project-mentee-count').text(mentee_count)

            let ratio = mentee_count && mentor_count ? `${Math.ceil(mentee_count / mentor_count)}/1` : 'N/A'
            $('#project-ratio').text(ratio)
        })
            .fail(function(error) {
                console.log(error)
            })
    }

    function loadMatches(){
        $.get(`/api/matches?id=${id}`, function(response) {
            // TODO: hide loading icons (still need to insert them)

            let matchContainer = $("#matches-container")
            $(matchContainer).empty()

            let responseJSON = JSON.parse(response)
            let matchCount = responseJSON.length
            let receivedData = ''

            $(responseJSON).each(function(match) {
                let row = `
                    <tr>
                        <td>${responseJSON[match]['match_id']}</td>
                        <td>${responseJSON[match]['date_created']}</td>
                        <td>${Math.round(responseJSON[match]['confidence_rate'] * 100)}%</td>
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
                $('#matches-container').html(receivedData)

            // load buttons for each row

            // adjust stats
            $('#project-num-matches').text(matchCount)
        })
            .fail(function(error) {
                console.log(error)
            })
    }

    function loadApplicantResponseButtons() {
        $('.applicant-response-button').click(function(event) {
            $("#question-responses-container").empty()
            let application_id = findParent(event, 'applicant-id').id
            adjustApplicantResponseModal(application_id)
        })
    }

    function adjustApplicantResponseModal(application_id) {
        // get info from api and load it into DOM
        $.get(`/api/responses?id=${id}&application_id=${application_id}`,
            function (response) {
                responses = JSON.parse(response)
                $(responses).each(function(index) {
                    console.log(responses[index]['question_text'])
                    let questionData =`
                        <div class="my-2">
                            <span>
                                <b>${responses[index]['question_text']}</b>
                            </span>
                            <br>
                            <span>${responses[index]['option_text']}</span>
                        </div>
                        `
                    $("#question-responses-container").append(questionData)
                })

            })
            .fail(function(response) {
                // show popup with error message
                console.log(response.responseText)
                window.alert(JSON.parse(response.responseText).message)
            })
    }

    function loadApplicantDeleteButtons() {
        $('.applicant-delete-button').click(function(event) {
            let application_id = findParent(event, 'applicant-id').id
            let confirmation = confirm(`Are you sure you wish to delete applicant ${application_id}? All matches with this applicant will also be deleted.`)
            if (confirmation) {
                deleteApplicant(application_id)
            }
        })
    }

    function deleteApplicant(application_id) {
        $.ajax({
            url: `/api/applicants?id=${id}&application_id=${application_id}` ,
            type: 'DELETE',
            success: function(result) {
                $(`#${application_id}`).remove()
            }
        });
    }

    function findParent (event, className) {
        let e = event.target
        while (e.classList.contains(className) == false) {
            e = e.parentNode
        }
        return e
    }
})