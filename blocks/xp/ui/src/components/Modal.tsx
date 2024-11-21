import { useCallback, useEffect, useRef, useState } from "react";
import * as ReactDOM from "react-dom";
import { useDuplicatedActionPreventor, useModules, useString } from "../lib/hooks";

export const SaveCancelModal: React.FC<{
  onClose?: () => void;
  onSave?: (e: Event) => void;
  show?: boolean;
  canSave?: boolean;
  title?: string;
  large?: boolean;
  saveButtonText?: string;
  defaultHeight?: number;
}> = ({ children, onClose, onSave, show, title, saveButtonText, defaultHeight, large, canSave = true }) => {
  const modalPromise = useRef<Promise<any>>();
  const modalRef = useRef<any>();
  // In rare instances, we can get double save events. This can happen when we hit enter,
  // and a new event listener is registered while Moodle is still broadcasting its events
  // which is then called, and so we get two events. This wouldn't happen if the modal was
  // not re-rendering, I think.
  const isSavePermitted = useDuplicatedActionPreventor();
  const { getModule } = useModules(["core/modal_factory", "core/modal_events"]);
  const [ready, setReady] = useState(false);

  const getSaveButton = useCallback((): HTMLButtonElement | null => {
    if (!modalRef.current) return null;
    const node = modalRef.current.getFooter()[0].querySelector('[data-action="save"]');
    return node ?? null;
  }, [modalRef.current]);

  const setSaveButtonText = (text?: string) => {
    const saveBtn = getSaveButton();
    if (!saveBtn || !text) return;
    saveBtn.textContent = text;
  };

  const setButtonAttribute = (attr: string, value: any) => {
    const saveBtn = getSaveButton();
    if (!saveBtn || !attr) return;
    if (value === null || typeof value === "undefined" || value === false) {
      saveBtn.removeAttribute(attr);
    } else {
      saveBtn.setAttribute(attr, value);
    }
  };

  // Create the modal object.
  useEffect(() => {
    let cancelled = false;
    if (modalRef.current) return;

    const ModalFactory = getModule("core/modal_factory");
    if (!ModalFactory) return;

    if (!modalPromise.current) {
      modalPromise.current = ModalFactory.create({
        type: ModalFactory.types.SAVE_CANCEL,
        title: title,
        large: large,
        body: `<div class='block_xp' style='${defaultHeight ? `height: ${defaultHeight}px` : ""}'></div>`,
      }) as Promise<any>;
    }

    modalPromise.current.then((modal) => {
      if (cancelled) return;

      modalRef.current = modal;

      if (saveButtonText) {
        setSaveButtonText(saveButtonText);
      }
      setReady(true); // State update to force re-render.

      if (show) {
        modal.show();
      }
    });

    return () => {
      cancelled = true;
    };
  });

  // Attach event listeners.
  useEffect(() => {
    const modal = modalRef.current;
    if (!modal) return;

    const ModalEvents = getModule("core/modal_events");
    if (!ModalEvents) return;

    const root = modal.getRoot();

    const handleSave = (e: Event) => {
      if (!isSavePermitted()) return;
      onSave && onSave(e);
    };
    const handleClose = () => {
      onClose && onClose();
    };

    // Keep the React node height in sync with the modal body to avoid for the modal
    // to become scrollable. This is required because our current modal content is
    // absolute and thus requires a hardcoded height.
    const updateReactNodeHeight = () => {
      const body = modal.getBody()[0];
      const reactNode = body ? body.querySelector(".block_xp") : null;
      if (!body || !reactNode) {
        return;
      }
      const height =
        body.clientHeight - (parseFloat(getComputedStyle(body).paddingTop) + parseFloat(getComputedStyle(body).paddingBottom));
      reactNode.style.height = `${height}px`;
    };

    const attachResize = () => {
      window.addEventListener("resize", updateReactNodeHeight);
    };

    root.on(ModalEvents.save, handleSave);
    root.on(ModalEvents.hidden, handleClose);
    root.on(ModalEvents.shown, attachResize);

    return () => {
      root.off(ModalEvents.save, handleSave);
      root.off(ModalEvents.hidden, handleClose);
      root.off(ModalEvents.shown, attachResize);
      window.removeEventListener("resize", updateReactNodeHeight);
    };
  });

  // Update visibility.
  useEffect(() => {
    if (!modalRef.current) return;
    if (show) {
      modalRef.current.show();
    } else {
      modalRef.current.hide();
    }
  }, [show, modalRef.current]);

  // Update title.
  useEffect(() => {
    if (!modalRef.current || !title) return;
    modalRef.current.setTitle(title);
  }, [title, modalRef.current]);

  // Update save button text.
  useEffect(() => {
    setSaveButtonText(saveButtonText);
  }, [saveButtonText, modalRef.current]);

  // Update the save button status.
  useEffect(() => {
    setButtonAttribute("disabled", !canSave);
  }, [canSave, modalRef.current]);

  return modalRef.current ? ReactDOM.createPortal(children, modalRef.current.getBody()[0].querySelector(".block_xp")) : null;
};

export const DeleteModal: React.FC<{
  onClose?: () => void;
  onDelete?: (e: Event) => void;
  show?: boolean;
  title?: string;
}> = ({ children, onClose, onDelete, show, title }) => {
  const modalPromise = useRef<Promise<any>>();
  const modalRef = useRef<any>();
  const [ready, setReady] = useState(false);
  const isDeletePermitted = useDuplicatedActionPreventor();
  const deleteStr = useString("delete", "core");
  const { getModule } = useModules(["core/modal_factory", "core/modal_events"]);

  const getDeleteButton = useCallback((): HTMLButtonElement | null => {
    if (!modalRef.current) return null;
    const node = modalRef.current.getFooter()[0].querySelector('[data-action="save"]');
    return node ?? null;
  }, [modalRef.current]);

  // Create the modal object.
  useEffect(() => {
    let cancelled = false;
    if (modalRef.current) return;

    const ModalFactory = getModule("core/modal_factory");
    if (!ModalFactory) return;

    if (!modalPromise.current) {
      modalPromise.current = ModalFactory.create({
        type: ModalFactory.types.SAVE_CANCEL, // We use save_cancel as delete_cancel is only in 4.2.
        title: title,
        body: `<div class='block_xp'></div>`,
      }) as Promise<any>;
    }

    modalPromise.current.then((modal) => {
      if (cancelled) return;

      modalRef.current = modal;

      const deleteButton = getDeleteButton();
      if (deleteButton) {
        if (deleteStr) {
          deleteButton.textContent = deleteStr;
        }
        deleteButton.classList.add("btn-danger");
      }
      setReady(true); // State update to force re-render.

      if (show) {
        modal.show();
      }
    });

    return () => {
      cancelled = true;
    };
  });

  // Attach event listeners.
  useEffect(() => {
    const modal = modalRef.current;
    if (!modal) return;

    const ModalEvents = getModule("core/modal_events");
    if (!ModalEvents) return;

    const root = modal.getRoot();

    const handleSave = (e: Event) => {
      if (!isDeletePermitted()) return;
      onDelete && onDelete(e);
    };
    const handleClose = () => {
      onClose && onClose();
    };

    root.on(ModalEvents.save, handleSave);
    root.on(ModalEvents.hidden, handleClose);

    return () => {
      root.off(ModalEvents.save, handleSave);
      root.off(ModalEvents.hidden, handleClose);
    };
  });

  // Update visibility.
  useEffect(() => {
    if (!modalRef.current) return;
    if (show) {
      modalRef.current.show();
    } else {
      modalRef.current.hide();
    }
  }, [show, modalRef.current]);

  // Update title.
  useEffect(() => {
    if (!modalRef.current || !title) return;
    modalRef.current.setTitle(title);
  }, [title, modalRef.current]);

  // Update button.
  useEffect(() => {
    if (!modalRef.current || !deleteStr) return;
    const btn = getDeleteButton();
    if (!btn) return;
    btn.textContent = deleteStr;
  }, [deleteStr, modalRef.current]);

  return modalRef.current ? ReactDOM.createPortal(children, modalRef.current.getBody()[0].querySelector(".block_xp")) : null;
};

export const ModalForm: React.FC<{
  formClass: string;
  formArgs?: Record<string, any>;
  onClose?: () => void;
  onSubmit?: () => void;
  title?: string;
}> = ({ formClass, formArgs, onClose, onSubmit, title }) => {
  const modalFormRef = useRef<any>();
  const { getModule } = useModules(["core_form/modalform", "core/modal_factory", "core/modal_events"]);

  // Create the modal form.
  useEffect(() => {
    if (modalFormRef.current) return;

    const ModalForm = getModule("core_form/modalform");
    if (!ModalForm) return;

    modalFormRef.current = new ModalForm({
      formClass: formClass,
      args: formArgs ?? {},
      modalConfig: {
        title,
      },
    });

    modalFormRef.current.show();
  });

  // Attach event listeners.
  useEffect(() => {
    const modalForm = modalFormRef.current;
    if (!modalForm) return;

    const ModalForm = getModule("core_form/modalform");
    const ModalEvents = getModule("core/modal_events");
    if (!ModalForm || !ModalEvents) return;

    const handleLoaded = () => {
      const root = modalForm.modal.getRoot();
      root[0].classList.add("block_xp");

      // Register the onClose event.
      root.on(ModalEvents.hidden, handleClose);
    };
    const handleSubmit = () => {
      onSubmit && onSubmit();
    };
    const handleClose = () => {
      onClose && onClose();
    };

    modalForm.addEventListener(modalForm.events.LOADED, handleLoaded);
    modalForm.addEventListener(modalForm.events.FORM_SUBMITTED, handleSubmit);
    modalForm.addEventListener(modalForm.events.CANCEL_BUTTON_PRESSED, handleClose);

    return () => {
      const modalForm = modalFormRef.current;
      const root = modalForm?.modal?.getRoot();
      const rootEl = root?.[0];
      rootEl?.removeEventListener(modalForm.events.LOADED, handleLoaded);
      rootEl?.removeEventListener(modalForm.events.FORM_SUBMITTED, handleSubmit);
      rootEl?.removeEventListener(modalForm.events.CANCEL_BUTTON_PRESSED, handleClose);
      root.off(ModalEvents.hidden, handleClose);
    };
  });

  useEffect(() => {
    if (!modalFormRef.current) return;
    const modal = modalFormRef.current.modal;
    if (!modal) return;
    modal.setTitle(title);
  }, [title]);

  return null;
};
