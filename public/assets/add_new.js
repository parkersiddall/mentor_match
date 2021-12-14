$(document).ready(function() {
    console.log("Ready")

    // handle new param button
    $("#add-question-button").click(function(event) {
        event.preventDefault()
        let questionContainer = $("#question-container")
        let newQuestion = createNewquestion()
        questionContainer.append(newQuestion)
        $(newQuestion).slideDown()
        loadRemoveQuestionButtons()
        loadAddOptionButtons()
        loadDeleteOptionButtons()
    })

    // handle delete param buttons
    const loadRemoveQuestionButtons = function() {
        $(".remove-question").each(function() {
            $(this).click(function(event) {
                let parent = $(findParent(event, "question"))
                $(parent).slideUp(300, function() {
                    $(parent).remove()
                })
            })
        })
    }
    loadRemoveQuestionButtons()

    // handle new option buttons
    const loadAddOptionButtons = function() {
        $(".add-option-button").each(function() {
            $(this).off()  // removes any event listeners that were previous attached to node
            $(this).click(function(event) {
                event.preventDefault()
                let optionContainer = $(event.target).siblings(".option-container")
                let newOption = addOption()
                optionContainer.append(newOption)
                loadDeleteOptionButtons()
            })
        })
    }
    loadAddOptionButtons()

    // handle delete option buttons
    const loadDeleteOptionButtons = function() {
        $(".delete-option-button").each(function() {
            $(this).click(function(event) {
                let parent = $(findParent(event, "option"))
                parent.remove()
            })
        })
    }
    loadDeleteOptionButtons()

    // handle submit button
    $("#submit-button").click(function(event) {
        event.preventDefault()
        // TODO add spinner
        let formData = collectFormData()
        $.post("/create", formData,
            function(response) {
                window.location.href = "/"
            })
            .fail(function(response) {
                // show popup with error message
                console.log(response.responseText)
                window.alert(JSON.parse(response.responseText).message)
            })
    })



    // helper functions
    const createNewquestion = function() {
        let question = document.createElement("div")
        $(question).hide()
        question.classList.add("question", "card", "text-dark", "bg-light", "mb-3")
        question.innerHTML = `
           <div class="card-header">
                <b>
                    Question
                </b>
                <button type="button" class="remove-question btn btn-sm btn-outline-danger float-end">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            <div class="card-body">
                <input type="text" class="question-name form-control mb-3" required>
                <div class="option-container">
                    <div class="option input-group input-group-sm mb-2">
                        <span class="input-group-text">Option</span>
                        <button class="delete-option-button btn btn-outline-danger" type="button">
                            <i class="bi bi-trash"></i>
                        </button>
                        <input type="text" class="option-name form-control" required>
                    </div>
                </div>
                <button class="add-option-button btn btn-sm btn-secondary">Add Option</button>
            </div>
        `
        // TODO: come back and check the HTML above when finished
        return question
    }

    const addOption = function() {
        let newOption = document.createElement("div")
        newOption.classList.add("option", "input-group", "input-group-sm", "mb-2")
        newOption.innerHTML = `
            <span class="input-group-text">Option</span>
            <button class="delete-option-button btn btn-outline-danger" type="button">
                <i class="bi bi-trash"></i>
            </button>
            <input type="text" class="option-name form-control" required>
        `
        // TODO: come back and check the HTML above when finished
        return newOption
    }

    const findParent = function(event, className) {
        let e = event.target
        while (e.classList.contains(className) == false) {
            e = e.parentNode
        }
        return e
    }

    const collectFormData = function() {
        let formData = {}
        formData.title = $("#title").val()
        formData.mentorDescription = $("#mentor-description").val()
        formData.menteeDescription = $("#mentee-description").val()
        formData.mentorApplicationStatus = $("#mentor-application-status").val()
        formData.menteeApplicationStatus = $("#mentee-application-status").val()
        formData.collectFirstName = $("#collect-first-name").is(':checked')
        formData.collectLastName = $("#collect-last-name").is(':checked')
        formData.collectEmail = $("#collect-email").is(':checked')
        formData.collectPhone = $("#collect-phone").is(':checked')
        formData.collectStudentID = $("#collect-student-id").is(':checked')

        formData.questions = []
        let questions = $(".question")
        for (let i = 0; i < questions.length; i++) {
            let newQuestion = {}
            newQuestion.priority = i
            newQuestion.question = $(questions[i]).find(".question-name").val()

            newQuestion.options = []
            let options = $(questions[i]).find(".option-name")
            for (let j = 0; j < options.length; j++) {
                newQuestion.options.push($(options[j]).val())
            }
            formData.questions.push(newQuestion)
        }

        return formData

    }
})