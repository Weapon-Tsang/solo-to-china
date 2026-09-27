# 当前文件边界与字节身份（2026-09-27）

以下是**待批准提交/发布清单**，未暂存、未commit/push、未打包/部署。原20条源码/脚本候选保持；其中父/子主题16文件是相对HEAD的产品增量，`search-backfill.php`是非web管理员库，另外3项为本地预览/验证脚本，不进入主题ZIP。实际完整主题包还须包含原未改依赖；全产品逐文件hash保存在本地 `output/fe02-final-accepted/product-source-hashes.json`（仅审计，不作为发布载荷）。

FE-01归属：栏目/列表紧凑布局、主/子CSS、归档模板；FE-02归属：搜索入口/表单/结果页、搜索CSS/JS、公开查询/投影与回填。functions/header/index/page/main.css等共享文件同时保留两阶段改动，不按单阶段覆盖。准确合并增量以当前工作树对HEAD的diff为准。本轮产品代码、合同、Tools和应用版本均未修改。

| 精确候选路径 | SHA-256 |
|---|---|
| `scripts/search-backfill.php` | `effe1d5d0b6b807f5393c0c2bd3f275640606d4bc85670ade55efdfb45d5956f` |
| `scripts/start-preview.ps1` | `be37a31494bc5b6a9bd2dad20b4f6d0f867034f304e723cfec63a10e72eb0854` |
| `scripts/verify-project.php` | `0b28f757175ccc3e989b53b32eedb896d2ab1b588a1ea2f4686ebec39d6f4466` |
| `scripts/verify-project.ps1` | `fec6bc4991e8802d83146d9a257094f5d6fefc608037163d111d70bc88604971` |
| `wp-content/themes/solo-to-china-child/assets/css/site.css` | `ab43e8c0f14727001723b97089429459f6d8066fa059ac075dc06f4269398cf8` |
| `wp-content/themes/solo-to-china-child/header.php` | `0879437d6c350c507fb1a0f6d9a6d28fb53e18a18fd9c2f178d845be9f2fad4e` |
| `wp-content/themes/solo-to-china/archive.php` | `e05bbae3c585d09b8529120e0b9b1460260b5a986a82c98cb57f03ebeb7aa0d8` |
| `wp-content/themes/solo-to-china/assets/css/main.css` | `a71e516643e0be8d0ec863fe85dfb50f74c58a06f6f74eefe0351221c951d18d` |
| `wp-content/themes/solo-to-china/assets/css/search.css` | `ce5989cb7112e908fb6883bd7d719efeeec64c1ec4335edeb2de8b7e5c9fb675` |
| `wp-content/themes/solo-to-china/assets/js/main.js` | `79bbee88ae2df3e1c167aa51c7f6ccf0ab79d19ae3e115cccdb248b3fc0fa287` |
| `wp-content/themes/solo-to-china/assets/js/search.js` | `1128cec011f6b79ea4f8a3796bd814df12f29a84592073ce83c4900af25693f6` |
| `wp-content/themes/solo-to-china/front-page.php` | `de8c0e27b46b62f13ec5479418f1a660f21cebd1a9e4bfdcf363cb5bc420f9f6` |
| `wp-content/themes/solo-to-china/functions.php` | `ca8d61a32cffa40aace7033417b18bc4e5c0148af6b479d5bd6953df377d11e0` |
| `wp-content/themes/solo-to-china/header.php` | `e539827c15efe5752b5b9b7ab5544a64c2bc3b41abd41a5a91fa433c5f2ee3a2` |
| `wp-content/themes/solo-to-china/inc/search-projection.php` | `08da33fd69afe77c61e8b8b5d26346c6dbd893169baa1291d5d993caddb8ce76` |
| `wp-content/themes/solo-to-china/inc/search.php` | `2ec34f392726f672f40bd430f6bd31c3be310df46e9b341542958d30ccf816fa` |
| `wp-content/themes/solo-to-china/index.php` | `c7c17b558ed9d78f11e60599c45ad150dd23d41af77a204d803c690f278b302a` |
| `wp-content/themes/solo-to-china/page.php` | `467115fa3516631bb92da9892125c4b269dd56da5271a3a805558d0e8d5bf2f6` |
| `wp-content/themes/solo-to-china/search.php` | `b6de7fc2a4acb5b3f95606c54332159b5cda587eee41ec8ab63be957354f4081` |
| `wp-content/themes/solo-to-china/searchform.php` | `b0d62e15280037da9ab6b6250af59057a05c9ac00b67bfd4980d6feaec17b6d1` |

## 既有制品身份（本轮只读核对，未重打包）

| 既有dist文件 | SHA-256 | 当前适用性 |
|---|---|---|
| `solo-to-china-child-theme.zip` | `bccc61a56b04a498a42f78d21f0ec24478bcb1b5a32f62a4679a04ac27f20a0a` | 历史包，与当前FE工作树不一致；禁止直接作为本轮发布包 |
| `solo-to-china-theme.zip` | `966f7766b6e9b3529e3969c4d9d23b5e3b73bd9d514b8472e84a6b5ba57f2fcb` | 历史包，与当前FE工作树不一致；禁止直接作为本轮发布包 |
| `solo-to-china-tools-plugin.zip` | `78e353776e37b2a605a3c99bc3f3a10ea572dc2cb17af4640f0d7e41a6fe1891` | 与当前源文件一致；本轮不升级 |

父主题历史包缺inc/search.php、inc/search-projection.php、assets/css/search.css、assets/js/search.js，并有12个不同文件；子主题header.php与site.css不同。旧manifest日期2026-09-23，虽然Parent0.33.11/Child0.13.1版本未变，不能当成现有FE字节。完整ZIP差异（含历史回滚包）见 `output/fe02-final-accepted/existing-artifacts.json`。所有旧包保留；发布前另按批准当前源树生成包并记录新SHA/条目，不覆盖未验证的回滚依据。

本轮文档改动精确列表：

- `docs/codex-frontend-upgrade/STATUS.md`
- `docs/codex-frontend-upgrade/DEPENDENCIES.md`
- `docs/codex-frontend-upgrade/acceptance/FE-02.md`
- `docs/codex-frontend-upgrade/acceptance/FE-02-closeout-v1.2.md`
- `docs/codex-frontend-upgrade/acceptance/FE-02-release-precheck.md`
- `docs/codex-frontend-upgrade/acceptance/FE-02-production-plan.md`
- `docs/codex-frontend-upgrade/acceptance/FE-02-index-operations.md`
- `docs/codex-frontend-upgrade/acceptance/FE-02-release-files.md`
- `docs/codex-frontend-upgrade/phases/FE-02-final-delivery-performance-accepted.txt`（完整输入原字节归档）

既有待提交文档/截图仍见下方原逐文件名单。新增本地审计仅在output/fe02-final-accepted，全部排除Git/部署；本轮未改变截图和原始JSON。提交前逐文件复核脱敏、权限和真实diff。主题包排除脚本、运行包、output、数据库、凭据、测试注入、私有日志和诊断端点。审批顺序及回滚只见[发布计划](FE-02-production-plan.md)，不执行旧文档中的历史部署授权。

---

## 以下为历史记录（当前决策以上文为准）

# FE-01 / FE-02 待提交文件审查清单

2026-09-27工作树候选清单，未git add/commit/push，非部署包。所有产品文件均为本轮开始前已有FE-01/FE-02成果，本轮只添加发布准备文档和本地审计证据。不能只选搜索新文件漏掉模板/父子主题和验证脚本依赖。

产品/脚本候选（逐项审核diff后才暂存）：

- `scripts/search-backfill.php`
- `scripts/start-preview.ps1`
- `scripts/verify-project.php`
- `scripts/verify-project.ps1`
- `wp-content/themes/solo-to-china-child/assets/css/site.css`
- `wp-content/themes/solo-to-china-child/header.php`
- `wp-content/themes/solo-to-china/archive.php`
- `wp-content/themes/solo-to-china/assets/css/main.css`
- `wp-content/themes/solo-to-china/assets/css/search.css`
- `wp-content/themes/solo-to-china/assets/js/main.js`
- `wp-content/themes/solo-to-china/assets/js/search.js`
- `wp-content/themes/solo-to-china/front-page.php`
- `wp-content/themes/solo-to-china/functions.php`
- `wp-content/themes/solo-to-china/header.php`
- `wp-content/themes/solo-to-china/inc/search-projection.php`
- `wp-content/themes/solo-to-china/inc/search.php`
- `wp-content/themes/solo-to-china/index.php`
- `wp-content/themes/solo-to-china/page.php`
- `wp-content/themes/solo-to-china/search.php`
- `wp-content/themes/solo-to-china/searchform.php`

交接文档和截图候选（逐项审核内容、脱敏、相对路径后才暂存）：

- `docs/codex-frontend-upgrade/DEPENDENCIES.md`
- `docs/codex-frontend-upgrade/STATUS.md`
- `docs/codex-frontend-upgrade/acceptance/FE-01-anchor.json`
- `docs/codex-frontend-upgrade/acceptance/FE-01-article.json`
- `docs/codex-frontend-upgrade/acceptance/FE-01-baseline.json`
- `docs/codex-frontend-upgrade/acceptance/FE-01-browser.json`
- `docs/codex-frontend-upgrade/acceptance/FE-01-combos.json`
- `docs/codex-frontend-upgrade/acceptance/FE-01-empty.json`
- `docs/codex-frontend-upgrade/acceptance/FE-01-home.json`
- `docs/codex-frontend-upgrade/acceptance/FE-01-images.json`
- `docs/codex-frontend-upgrade/acceptance/FE-01-metadata.json`
- `docs/codex-frontend-upgrade/acceptance/FE-01-nav-only.json`
- `docs/codex-frontend-upgrade/acceptance/FE-01-single.json`
- `docs/codex-frontend-upgrade/acceptance/FE-01-zoom.json`
- `docs/codex-frontend-upgrade/acceptance/FE-01.md`
- `docs/codex-frontend-upgrade/acceptance/FE-02-browser.json`
- `docs/codex-frontend-upgrade/acceptance/FE-02-closeout-v1.2.md`
- `docs/codex-frontend-upgrade/acceptance/FE-02-closeout.md`
- `docs/codex-frontend-upgrade/acceptance/FE-02-index-operations.md`
- `docs/codex-frontend-upgrade/acceptance/FE-02-performance.json`
- `docs/codex-frontend-upgrade/acceptance/FE-02-production-plan.md`
- `docs/codex-frontend-upgrade/acceptance/FE-02-projection-inventory.md`
- `docs/codex-frontend-upgrade/acceptance/FE-02-release-files.md`
- `docs/codex-frontend-upgrade/acceptance/FE-02-release-precheck.md`
- `docs/codex-frontend-upgrade/acceptance/FE-02.md`
- `docs/codex-frontend-upgrade/phases/FE-01.txt`
- `docs/codex-frontend-upgrade/phases/FE-02-closeout-v1.1.txt`
- `docs/codex-frontend-upgrade/phases/FE-02-closeout-v1.2.txt`
- `docs/codex-frontend-upgrade/phases/FE-02-release-precheck-v1.0.txt`
- `docs/codex-frontend-upgrade/phases/FE-02.txt`
- `docs/codex-frontend-upgrade/screenshots/article-share-390.png`
- `docs/codex-frontend-upgrade/screenshots/attraction-390.png`
- `docs/codex-frontend-upgrade/screenshots/baseline-attraction-390.png`
- `docs/codex-frontend-upgrade/screenshots/baseline-city-390.png`
- `docs/codex-frontend-upgrade/screenshots/baseline-city-archive-390.png`
- `docs/codex-frontend-upgrade/screenshots/city-1440.png`
- `docs/codex-frontend-upgrade/screenshots/city-390.png`
- `docs/codex-frontend-upgrade/screenshots/city-archive-390.png`
- `docs/codex-frontend-upgrade/screenshots/city-multi-390.png`
- `docs/codex-frontend-upgrade/screenshots/empty-city-390.png`
- `docs/codex-frontend-upgrade/screenshots/fe02-city-390.png`
- `docs/codex-frontend-upgrade/screenshots/fe02-dialog-390.png`
- `docs/codex-frontend-upgrade/screenshots/fe02-home-1280.png`
- `docs/codex-frontend-upgrade/screenshots/fe02-home-390.png`
- `docs/codex-frontend-upgrade/screenshots/fe02-no-tools-results-390.png`
- `docs/codex-frontend-upgrade/screenshots/fe02-parent-city-390.png`
- `docs/codex-frontend-upgrade/screenshots/fe02-results-390.png`
- `docs/codex-frontend-upgrade/screenshots/home-390.png`
- `docs/codex-frontend-upgrade/screenshots/long-term-320-200pct.png`
- `docs/codex-frontend-upgrade/screenshots/no-tools-city-390.png`
- `docs/codex-frontend-upgrade/screenshots/parent-city-390.png`
- `docs/codex-frontend-upgrade/screenshots/single-no-nav-390.png`

排除整个 `output/`、`.tools/`、`node_modules/`、`dist/`、缓存、数据库、私有fixture、凭据、原始日志、测试控制端点和故障注入后门。旧output证据仅本地保留，不删除。`scripts/start-preview.ps1`是本地测试启动器，提交审核与生产打包分开；`scripts/search-backfill.php`只能放在获准管理员上下文，现有主题ZIP不会包含它，不上传为可调用web端点。

建议暂存后（本轮未暂存）执行 `git diff --cached --name-status`、`git diff --cached --check`、`git diff --cached`，对照以上精确白名单，人工检查秘密、测试路由/屏障和生产URL写调用；审核新增未跟踪文件不能只看git diff。不可使用git add .。准备包时独立列SHA、工作树身份、实际文件清单；版本号未提升，不能仅以0.33.11判断代码一致。当前没有打包，避免覆盖旧dist制品。

父/子主题旧改动包括FE-01首屏与FE-02入口/UI/查询；本轮没有改写这些成果。CMS合同/Tools产品源码未因本轮改变。
