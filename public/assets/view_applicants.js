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
    load_applicants()

    setInterval(function() {
        load_applicants()
    }, 10000)



    // event listeners
    $("#make-matches").click(function(event) {
        let formData = {
            "action" : "make_matches"
        }
        $.post(`/project?id=${id}`, formData,
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
                $('#match-data').html(receivedData)
            })
            .fail(function(response) {
                // show popup with error message
                window.alert(JSON.parse(response.responseText).message)
            })
    })

    $("#delete-match-form").click(function(event) {
        confirmDelete = confirm("Are you sure you wish to delete this match form? All data will be lost.")
        if(confirmDelete == true) {
            let formData = {
                "action" : "delete_match_form"
            }
            $.post(`/project?id=${id}`, formData,
                function (response) {
                    // TODO: show success notification
                    window.location.href = "/"
            })
            .fail(function(response) {
                // show popup with error message
                console.log(response.responseText)
                window.alert(JSON.parse(response.responseText).message)
            })
        }
    })

    // helper functions
    function load_applicants() {
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
                <tr>
                    <td>${applicants[applicant]['application_id']}</td>
                    <td>${applicants[applicant]['date_created']}</td>
                    <td>${applicants[applicant]['first_name']}</td>
                    <td>${applicants[applicant]['last_name']}</td>
                    <td>${applicants[applicant]['email']}</td>
                    <td>${applicants[applicant]['phone']}</td>
                    <td>${applicants[applicant]['stud_id']}</td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
                            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#applicantDetailsModal">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle" viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                    <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                                </svg>
                            </button>
                            <button type="button" class="btn btn-outline-secondary">
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
                    console.log(mentor_count)
                } else if (applicants[applicant]['m_type'] == "mentee") {
                    menteeContainer.append(applicantRow)
                    mentee_count++
                }
            })

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
})