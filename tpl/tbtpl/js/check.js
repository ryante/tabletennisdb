$(document).ready(function() {
    $("#form").SetValidateSettings({ Config: { AutoSubmit: false} });
    $("#name").SetValidateSettings({
        FormValidate: {
            Empty: {
                Value: true,
                Message: "用户名不能为空！"
            }
        },
        Message: {
            Text: {
                Show: "",
                Success: "输入正确！",
                Error: "必须输入用户名！",
                Focus: "正在输入..."
            },
            MessageSpaceHolderID: "name_error"
        }
    });
});

$(document).ready(function() {
    $("#form").SetValidateSettings({ Config: { AutoSubmit: false} });
    $("#email").SetValidateSettings({
        FormValidate: {
            Empty: {
                Value: true,
                Message: "邮件不能为空！"
            },
            Format: {
                Value: /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/,
                Message: "邮件格式不正确！"
            }
        },
        Message: {
            Text: {
                Show: "",
                Success: "输入正确！",
                Error: "邮件格式错误！",
                Focus: "正在输入..."
            },
            MessageSpaceHolderID: "email_error"
        }
    });
});

$(document).ready(function() {
    $("#form").SetValidateSettings({});
    $("#content").SetValidateSettings({
        FormValidate: {
            Empty: {
                Value: true,
                Message: "内容不能为空！"
            }
        },
        Message: {
            Text: {
                Show: "",
                Success: "输入正确！",
                Error: "内容不能为空！",
                Focus: "正在输入..."
            },
            MessageSpaceHolderID: "content_error"
        }
    });
});




