$(document).ready(function() {
    $.get("/api/projects", function(response) {
        $('#loading-icon').hide()
        let projectsContainer = $('#project-container')
        let projects = JSON.parse(response)
        $(projects).each(function(project) {

            let mentorAppStatus = projects[project]['mentor_app_status'] ? "OPEN" : "CLOSED"
            let menteeAppStatus = projects[project]['mentee_app_status'] ? "OPEN" : "CLOSED"

            projectHTML = `
                <a href="/project?id=24" style="text-decoration: none; color: black">
                    <div class="row py-3 mb-2" style="border-radius: 4px; border: 1px solid rgba(0, 0, 0, 0.125)">
                        <div class="col-8 my-auto">
                            <h5>${projects[project]['title']}</h5>
                            <small>${projects[project]['date_created']}</small>
                        </div>
                        <div class="col-2 text-center my-auto">
                            <h6>${mentorAppStatus}</h6>
                            <small>Mentor Application</small>
                        </div>
                        <div class="col-2 text-center my-auto">
                            <h6>${menteeAppStatus}</h6>
                            <small>Mentee Application</small>
                        </div>
                    </div>
                </a>
            `
            projectsContainer.append(projectHTML)
        })
    })
    .fail(function(error) {
        console.log(error)
    })
})