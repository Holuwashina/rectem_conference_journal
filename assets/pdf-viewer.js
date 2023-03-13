params = new URLSearchParams(window.location.search);
pdf = params.get("pdf");

if (!pdfjsLib.getDocument || !pdfjsViewer.PDFViewer) {
  // eslint-disable-next-line no-alert
  alert("Please build the pdfjs-dist library using\n  `gulp dist-install`");
}

// The workerSrc property shall be specified.
//
pdfjsLib.GlobalWorkerOptions.workerSrc =
  "/assets/pdfjs-dist/build/pdf.worker.js";

// Some PDFs need external cmaps.
//
const CMAP_URL = "./pdfjs-dist/cmaps/";
const CMAP_PACKED = true;

const DEFAULT_URL = `/uploads/${pdf}`;

const ENABLE_XFA = true;
const SEARCH_FOR = ""; // try "Mozilla";
const SANDBOX_BUNDLE_SRC = "./pdfjs-dist/build/pdf.sandbox.js";

const container = document.getElementById("viewerContainer");

const eventBus = new pdfjsViewer.EventBus();

// (Optionally) enable hyperlinks within PDF files.
const pdfLinkService = new pdfjsViewer.PDFLinkService({
  eventBus,
});

// (Optionally) enable find controller.
const pdfFindController = new pdfjsViewer.PDFFindController({
  eventBus,
  linkService: pdfLinkService,
});

// (Optionally) enable scripting support.
const pdfScriptingManager = new pdfjsViewer.PDFScriptingManager({
  eventBus,
  sandboxBundleSrc: SANDBOX_BUNDLE_SRC,
});

const pdfViewer = new pdfjsViewer.PDFViewer({
  container,
  eventBus,
  linkService: pdfLinkService,
  findController: pdfFindController,
  scriptingManager: pdfScriptingManager,
});
pdfLinkService.setViewer(pdfViewer);
pdfScriptingManager.setViewer(pdfViewer);

eventBus.on("pagesinit", function () {
  // We can use pdfViewer now, e.g. let's change default scale.
  pdfViewer.currentScaleValue = "page-width"; // We can try searching for things.

  if (SEARCH_FOR) {
    eventBus.dispatch("find", { type: "", query: SEARCH_FOR });
  }
});

// Loading document.
const loadingTask = pdfjsLib.getDocument({
  url: DEFAULT_URL,
  cMapUrl: CMAP_URL,
  cMapPacked: CMAP_PACKED,
  enableXfa: ENABLE_XFA,
});
(async function () {
  const pdfDocument = await loadingTask.promise; // Document loaded, specifying document for the viewer and // the (optional) linkService.
  pdfViewer.setDocument(pdfDocument);

  pdfLinkService.setDocument(pdfDocument, null);
})();
