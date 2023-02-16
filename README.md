# newsletter-core 

To install the newsletter-core Module properly into your n2n project you must register a BatchJob e.g. newsletter\impl\controller\NewsletterBatchJob in your project's app.ini
containing a method call from HistoryEntryGenerator::buildHistoryEntriesForFirstUnpreparedHistory & SendBatchDao::sendMails after setting up the NewsletterState for your needs.
