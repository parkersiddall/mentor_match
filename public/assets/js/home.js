$(document).ready(function() {
    $.get("/api/projects", function(response) {
        $('#loading-icon').hide()
        let projectsContainer = $('#project-container')
        let projects = JSON.parse(response)
        $(projects).each(function(project) {

            let mentorAppStatus = projects[project]['mentor_app_open'] == true? "OPEN" : "CLOSED"
            let menteeAppStatus = projects[project]['mentee_app_open'] == true ? "OPEN" : "CLOSED"

            projectHTML = `
                <a href="/project?id=${projects[project]['match_form_id']}" style="text-decoration: none; color: black">
                    <div class="row py-3 mb-2 project-card">
                        <div class="col-8 my-auto">
                            <h6>${projects[project]['title']}</h5>
                            <small class="project-date-created">${projects[project]['date_created']}</small>
                        </div>
                        <div class="col-2 text-center my-auto">
                            <h6 class="${mentorAppStatus == "OPEN" ? 'app-status-open' : 'app-status-closed'}">${mentorAppStatus}</h6>
                            <small class="app-status-subtitle">Mentor Application</small>
                        </div>
                        <div class="col-2 text-center my-auto">
                            <h6 class="${menteeAppStatus == "OPEN" ? 'app-status-open' : 'app-status-closed'}">${menteeAppStatus}</h6>
                            <small class="app-status-subtitle">Mentee Application</small>
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