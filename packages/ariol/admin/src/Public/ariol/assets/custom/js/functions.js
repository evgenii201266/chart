/* Уведомления. */
function notice(type, message, width, timeout, layout) {
    width = width || 200;
    timeout = timeout || 200;
    layout = layout || 'topRight';

    noty({
        type: type,
        width: width,
        text: message,
        layout: layout,
        timeout: timeout,
        dismissQueue: true
    });
}

/* Уведомление с получением типа. */
function resultNotice(result) {
    var type = result.error ? 'error' : 'success';
    var message = result.error ? result.error : result.success;

    notice(type, message);
}