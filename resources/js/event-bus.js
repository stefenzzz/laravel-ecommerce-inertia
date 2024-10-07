import mitt from 'mitt';

export const SHOW_NOTIFICATION = 'SHOW_NOTIFICATION';
export const SHOW_NOTIFICATION_DURATION = ' SHOW_NOTIFICATION_DURATION';
export const PULSE_LOAD = 'PULSE_LOAD';

export const emitter = mitt();

export function showSuccessNotification(message) {
    emitter.emit(SHOW_NOTIFICATION, {type: 'success', message});
}
export function showErrorNotification(message) {
    emitter.emit(SHOW_NOTIFICATION, {type: 'error', message});
}

export function showNotifiation(message, duration) {
    const [[typ, msg]] = Object.entries(message); // destruct the message get the key and value separately
    emitter.emit(SHOW_NOTIFICATION_DURATION, {type: typ, _message:msg, delay:duration});
}
