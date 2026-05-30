
export const LoadingModal = (text = 'Loading...') => {
  return Swal.fire({
    text: text,
    allowOutsideClick: false,
    allowEscapeKey: false,
    preConfirm: () => false,
    width: '200px',
  }).then(Swal.showLoading());
}
export const MessageModal = async (options, callback) => {
  return await Swal.fire({
    ...options,
    showConfirmButton: false,
  }).then(async () => {
    if (typeof callback === "function") {
      return await callback();
    }
  })
}
export const CloseModal = () => {
  return Swal.close();
}

export const ErrorModal = async (error) => {
  if (!error.response) {
    throw error;
  }
  if (error.response.status === 401) {
    return await MessageModal(
      "error",
      "SESSION EXPIRED",
      error.response.data.message ?? error.message,
      () => window.location.reload(),
    );
  }
  if (error.response.status === 403) {
    return await MessageModal(
      "error",
      "SERVER REJECTION",
      error.response.data.message ?? error.message,
    );
  }
  if (error.response.status === 404) {
    return await MessageModal(
      "error",
      "NOT FOUND",
      error.response.data.message ?? error.message,
    );
  }
  if (error.response.status === 406) {
    return await MessageModal(
      "warning",
      "UNACCEPTABLE",
      error.response.data.message ?? error.message,
    );
  }
  if (error.response.status === 417) {
    return await MessageModal(
      "error",
      "EXPECTATION FAILED",
      error.response.data.message ?? error.message,
      () => window.location.reload(),
    );
  }
  if (error.response.status === 422) {
    return await MessageModal(
      "error",
      "ACTION FAILED",
      error.response.data.message ?? error.message,
    );
  }
  if (error.response.status === 429) {
    return await MessageModal(
      "error",
      "SERVER REJECTION",
      "Too Many Attempts! Please wait...",
    );
  }
  if (error.response.status === 500) {
    return await MessageModal(
      "error",
      "SERVER ERROR",
      error.response.data.message ?? error.message,
    );
  }
};
