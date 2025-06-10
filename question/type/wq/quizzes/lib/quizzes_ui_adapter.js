
const quizzes = com.wiris.quizzes.api.Quizzes.getInstance();
const { configure, renderAnswerField } = await import(quizzes.getResourceUrl('quizzes.ui.js'));

configure({
    apiUrl: quizzes.getConfiguration().get(com.wiris.quizzes.api.ConfigurationKeys.API_URL),
    grammarUrl: `${quizzes.getConfiguration().get(com.wiris.quizzes.api.ConfigurationKeys.SERVICE_URL)}/grammar/Math`
});

const parseSlot = (slot) => {
    return {
        syntax: parseSyntax(slot.getSyntax()) 
    }
};

const parseSyntax = (syntax) => {
    const name = syntax.getName();

    if (name == com.wiris.quizzes.api.assertion.SyntaxName.MATH) {
        return {
            name: 'math',
            parameters: {}
        }
    } else if (name == com.wiris.quizzes.api.assertion.SyntaxName.MATH_MULTISTEP) {
        const taskType = syntax.getParameter(com.wiris.quizzes.api.assertion.SyntaxParameterName.TYPE_OF_TASK);

        const parameters = {
            taskType,
            taskToSolve: syntax.getParameter(com.wiris.quizzes.api.assertion.SyntaxParameterName.TASK_TO_SOLVE),
            refId: syntax.getParameter(com.wiris.quizzes.api.assertion.SyntaxParameterName.REF_ID)
        };

        if (taskType == 'single_variable_equation') {
            parameters.equationVariableName = syntax.getParameter(com.wiris.quizzes.api.assertion.SyntaxParameterName.VARIABLE_NAME)
        }

        return {
            name: 'math_multistep',
            parameters
        }
    }
}

const parseQuestionInstance = (instance) => {
    const data = {};

    const sessionId = instance.getProperty(com.wiris.quizzes.api.PropertyName.MULTISTEP_SESSION_ID);
    if (sessionId) {
        data.multiStepSessionId = sessionId;
    }

    return {
        data
    }
};

if (window.com == null) {
    window.com = {};
}

if (window.com.wiris == null) {
    window.com.wiris = {};
}

if (window.com.wiris.js == null) {
    window.com.wiris.js = {};
}

window.com.wiris.js.QuizzesUiAdapter = {
    buildAnswerField(el, slot, instance, listener) {
        renderAnswerField(el, parseSlot(slot), parseQuestionInstance(instance), listener);
    }
}