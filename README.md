# Scheduler Forker

A TYPO3 extension that runs every scheduler task in a separate process.

## Why?

The TYPO3 scheduler command `typo3 scheduler:run` executes all overdue tasks in
the same process.

When using Extbase (especially the ConfigurationManager) you'll end up in unexpected results
when using a multipage setup.

The reason is that the ConfigurationManager has a static cache which means if you have
initialized a TYPO3 configuration (TypoScript) it will be used for all tasks executed
after it. There is no way to reset the ConfigurationManager and force it to load a
configuration for a different page.

## How to use

Just call `typo3 scheduler_forker:run` instead of `typo3 scheduler:run`.
The scheduler_forker will then call `typo3 scheduler:run --task X` for each overdue task.
