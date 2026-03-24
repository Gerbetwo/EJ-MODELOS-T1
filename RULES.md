# PHP Project Quality Guidelines

This file dictates the strict rules to be followed when generating or modifying code for this project.

## 1. Zero Inline Styles Policy
Under **NO circumstances** are inline `style="..."` attributes permitted in the HTML/`.phtml` views.
- **Rule:** Every visual customization must be defined as a CSS class inside the `public/assets/css/custom/` directories.
- **Reasoning:** Inline styles pollute the DOM, make maintenance difficult, violate the Separation of Concerns (SoC) principle, and break CSP (Content Security Policies).

## 2. Strict UI Component Reusability
- UI patterns (tables, forms, buttons) should use predefined classes from the existing CSS structure (`_components.css`, `main.css`).
- E.g., `.data-table-wrapper`, `.data-table-search`, `.action-btn`, `.crud-modal`.

## 3. Clean Architecture Adherence
- **Controllers:** Controllers must never contain raw SQL, nor instantiate models directly using `new` (except via DI or factories). They consume `CrudService` or similar use-case structures.
- **Views:** Views `.phtml` must only contain presentation logic (HTML, small `foreach` loops, variables output). NO querying the database or containing complex PHP logic inside a view.
- **Entities / Models:** Data models define attributes (`#[Table]`, `#[Column]`).

## 4. Modern DOM Manipulation
- Javascript must interact with the DOM using semantic, prefix-based selectors whenever possible (e.g. `.btn-edit-js`).
- Modals must preferentially utilize the native HTML5 `<dialog>` API for top-layer safety, accessibility, and zero z-index conflicts.

*Any future changes must be audited against these rules.*
