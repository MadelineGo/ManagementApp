# Shared Module

This module contains reusable UI components, pipes, and directives that are used across multiple features.

## Contents
- **components/**: "Dumb" components that receive data via inputs and emit events via outputs (e.g., Buttons, Cards, Inputs).
- **pipes/**: Custom pipes for data transformation.
- **directives/**: Custom directives for DOM manipulation.
- **ui/**: Layout components or UI library wrappers.

**Note:** This module should NOT have dependencies on Feature modules. It should be imported by Features that need these shared resources.
